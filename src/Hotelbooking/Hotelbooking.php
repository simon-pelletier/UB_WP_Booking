<?php
namespace Hotelbooking;

if ( ! defined( 'ABSPATH' ) ) exit;

use Hotelbooking\Models\HooksInterface;
use Hotelbooking\Models\HooksFrontInterface;
use Hotelbooking\Models\HooksAdminInterface;

class Hotelbooking implements HooksInterface{

    protected $actions   = array();

    public function __construct($actions = array()){
        $this->actions = $actions;
    }

    protected function canBeLoaded(){
        return true;
    }

    public function execute(){
        if ($this->canBeLoaded()){
            add_action( 'plugins_loaded' , array($this,'hooks'), 0);
        }
    }

    public function getActions(){
        return $this->actions;
    }
    
    public function hooks(){
        foreach ($this->getActions() as $key => $action) {
            switch(true) {  // Cela m'évite de faire un if / else if
                case $action instanceof HooksAdminInterface:
                    if (is_admin()) {
                        $action->hooks();
                    }
                    break;
                case $action instanceof HooksFrontInterface:
                    if (!is_admin()) {
                        $action->hooks();
                    }
                    break;
                case $action instanceof HooksInterface:
                    $action->hooks();
                    break;
            }
        }
    }

}
