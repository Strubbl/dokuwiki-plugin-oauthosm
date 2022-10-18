<?php

namespace dokuwiki\plugin\oauthosm;

use dokuwiki\plugin\oauth\Service\AbstractOAuth2Base;
use OAuth\Common\Http\Uri\Uri;

/**
 * Custom Service for OpenStreetMap oAuth
 */
class OpenStreetMap extends AbstractOAuth2Base
{
    /** @inheritdoc */
    public function needsStateParameterInAuthUrl() {
        $plugin = plugin_load('helper', 'oauthosm');
        return 0 !== $plugin->getConf('needs-state');
    }

    /** @inheritdoc */
    public function getAuthorizationEndpoint()
    {
        $plugin = plugin_load('helper', 'oauthosm');
        return new Uri($plugin->getConf('authurl'));
    }

    /** @inheritdoc */
    public function getAccessTokenEndpoint()
    {
        $plugin = plugin_load('helper', 'oauthosm');
        return new Uri($plugin->getConf('tokenurl'));
    }

    /**
     * @inheritdoc
     */
    protected function getAuthorizationMethod()
    {
        $plugin = plugin_load('helper', 'oauthosm');

        return (int) $plugin->getConf('authmethod');
    }
}
