<?php

use dokuwiki\plugin\oauth\Adapter;
use dokuwiki\plugin\oauthosm\DotAccess;
use dokuwiki\plugin\oauthosm\OpenStreetMap;

/**
 * Service Implementation for oAuth OpenStreetMap authentication
 */
class action_plugin_oauthosm extends Adapter
{

    /** @inheritdoc */
    public function registerServiceClass()
    {
        return OpenStreetMap::class;
    }

    /** * @inheritDoc */
    public function getUser()
    {
        $oauth = $this->getOAuthService();
        $data = array();

        $url = $this->getConf('userurl');
        $raw = $oauth->request($url);

        if (!$raw) throw new OAuthException('Failed to fetch data from userurl');
        $result = json_decode($raw, true);
        if (!$result) throw new OAuthException('Failed to parse data from userurl');

        $user = DotAccess::get($result, $this->getConf('json-user'), '');
        $name = DotAccess::get($result, $this->getConf('json-name'), '');
        $mail = DotAccess::get($result, $this->getConf('json-mail'), '');
        $grps = DotAccess::get($result, $this->getConf('json-grps'), []);

        // type fixes
        if (is_array($user)) $user = array_shift($user);
        if (is_array($name)) $name = array_shift($name);
        if (is_array($mail)) $mail = array_shift($mail);
        if (!is_array($grps)) {
            $grps = explode(',', $grps);
            $grps = array_map('trim', $grps);
        }

        // fallbacks for user name
        if (empty($user)) {
            if (!empty($name)) {
                $user = $name;
            } elseif (!empty($mail)) {
                list($user) = explode('@', $mail);
            }
        }

        // fallback for full name
        if (empty($name)) {
            $name = $user;
        }

        // OSM does not provide the user's mail, so let's generate a fictional one being used only dokuwiki-internal
        $mail .= "@openstreetmap.org";

        return compact('user', 'name', 'mail', 'grps');
    }

    /** @inheritdoc */
    public function getScopes()
    {
        return $this->getConf('scopes');
    }

    /** @inheritDoc */
    public function getLabel()
    {
        return $this->getConf('label');
    }

    /** @inheritDoc */
    public function getColor()
    {
        return $this->getConf('color');
    }
}
