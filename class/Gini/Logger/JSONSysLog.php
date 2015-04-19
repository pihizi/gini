<?php

namespace Gini\Logger;

class JSONSysLog extends Handler
{
    private static $_LEVEL2PRIORITY = [
        Level::EMERGENCY => \LOG_EMERG,
        Level::ALERT => \LOG_ALERT,
        Level::CRITICAL => \LOG_CRIT,
        Level::ERROR => \LOG_ERR,
        Level::WARNING => \LOG_WARNING,
        Level::NOTICE => \LOG_NOTICE,
        Level::INFO => \LOG_INFO,
        Level::DEBUG => \LOG_DEBUG,
    ];

    public function log($level, $message, array $context = [])
    {
        if (!$this->isLoggable($level)) {
            return;
        }

        $replacements = [];
        $_fillReplacements = function (&$replacements, $context, $prefix = '') use (&$_fillReplacements) {
            foreach ($context as $key => $val) {
                if (is_array($val)) {
                    $_fillReplacements($replacements, $val, $prefix.$key.'.');
                } else {
                    $replacements['{'.$prefix.$key.'}'] = $val;
                }
            }
        };
        $_fillReplacements($replacements, $context);

        $context['@ident'] = $this->_name;
        $context['@message'] = strtr($message, $replacements);

        $message = '@cee: '.J($context);

        openlog(APP_ID, LOG_ODELAY, LOG_LOCAL0);
        syslog(self::$_LEVEL2PRIORITY[$level], $message);
        closelog();
    }
}
