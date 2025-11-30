<?php

namespace App\Tags;

use Statamic\Tags\Tags;

class Captcha extends Tags
{
    /**
     * Default handler: {{ captcha }}
     */
    public function index()
    {
        return $this->renderCaptcha();
    }

    /**
     * Handler for wildcard tags (ex: {{ captcha js="false" }})
     */
    public function wildcard($tag)
    {
        if ($tag === 'js' && $this->params->get('js') === 'false') {
            // Modal-safe version, but now with optional modal flag
            return $this->renderCaptcha(true);
        }

        return $this->index();
    }

    /**
     * Generate HTML for reCAPTCHA container
     *
     * @param bool $forceModal When true, marks this captcha as for modal use only.
     */
    private function renderCaptcha($forceModal = false)
    {
        $sitekey = config('services.recaptcha.key');

        // Detect modal flag (modal="true" OR {{ captcha js="false" }})
        $isModal = $forceModal || $this->params->bool('modal', false);

        if ($isModal) {
            // MODAL VERSION — manual render only
            return <<<HTML
<div id="modalCaptcha"></div>
<input type="hidden" name="g_recaptcha_response" id="modalCaptchaResponse">
HTML;
        }

        // NORMAL VERSION — allow Google auto-render
        return <<<HTML
<div class="g-recaptcha" data-sitekey="{$sitekey}"></div>
<input type="hidden" name="g_recaptcha_response">
HTML;
    }

}
