<?php

class Modules_JoomlaToolkit_CustomButtons extends pm_Hook_CustomButtons
{
    public function getButtons()
    {
        return [
            [
                'place' => self::PLACE_COMMON,
                'title' => pm_Locale::lmsg('components.customButtons.adminJoomlaToolkit'),
                'description' => pm_Locale::lmsg('components.customButtons.adminJoomlaToolkitDescription'),
                //'icon' => pm_Context::getBaseUrl() . 'images/icon.png', // TODO: add icon
                'link' => pm_Context::getActionUrl('index', 'list'),
                'hidden' => !pm_Session::getClient()->isAdmin(),
            ], [
                'place' => self::PLACE_DOMAIN,
                'title' => pm_Locale::lmsg('components.customButtons.customerJoomlaToolkit'),
                'description' => pm_Locale::lmsg('components.customButtons.customerJoomlaToolkitDescription'),
                //'icon' => pm_Context::getBaseUrl() . 'images/icon.png', // TODO: add icon
                'link' => pm_Context::getActionUrl('index', 'list'),
                'hidden' => !pm_Session::getClient()->isClient(),
            ],
        ];
    }
}
