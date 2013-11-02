<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

class action_plugin_maintenance extends DokuWiki_Action_Plugin {

    function __construct() {
        global $conf;
        $this->temp_dir = $conf['tmpdir'].'/maintenance';
        $this->disallowed = array('register','resendpwd','profile','edit','draft','draftdel','preview','save','subscribe','unsubscribe');
    }

    /**
     * register the eventhandlers
     */
    function register(&$contr){
        $lock_file = $this->temp_dir.'/lock';
        if (is_file($lock_file)) {
            $contr->register_hook('DOKUWIKI_STARTED', 'BEFORE', $this, 'before_start', array());
            $contr->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'before_action', array());
        }
    }

    function before_start() {
        msg($this->getConf('msg_lock'));
    }

    function before_action(&$event, $param) {
        global $ACT;
        $act = act_clean($ACT);
        if (!in_array($act, $this->disallowed)) return;
        msg('Command disabled: '.htmlspecialchars($act),-1);
        $ACT = 'show';
        $event->preventDefault();
    }
}
