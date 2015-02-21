<?php
// Google+ extension for Bolt

namespace Bolt\Extension\DanielKulbe\PHPinfo;

use Bolt\Application;
use Bolt\BaseExtension;

class Extension extends BaseExtension
{
    /**
     * Extension name
     *
     * @var string
     */
    const NAME = 'PHP Info';

    /**
     * Cache identifer
     * @var string
     */
    private $cachekey;

    /**
     * Cache duration
     * @var integer
     */
    private $cachetime;#


    /**
     * Add Twig settings in 'frontend' environment
     *
     * @return void
     */
    public function initialize()
    {
        $this->cachekey  = 'phpinfo';
        $this->cachetime = intval($this->app['config']->get('general/caching/duration', 10)) *60;
        $this->addTwigFunction('phpinfo', 'phpInfo');
    }


    /**
     * Get the extension's human readable name
     *
     * @return string
     */
    public function getName()
    {
        return Extension::NAME;
    }


    /**
     * Make phpinfo() Twig function save for record fields, when `allowtwig` is set to `true`.
     *
     * @return boolean Safe to use with html fields
     */
    public function isSafe()
    {
        return true;
    }


    /**
     * Set the defaults for configuration parameters
     *
     * @return array
     */
    public function phpInfo()
    {
        $phpinfo = null;

        if ($this->app['cache']->contains($this->cachekey)) {
            $phpinfo = base64_decode($this->app['cache']->fetch($this->cachekey));
        } else {
            ob_start();
            phpinfo();
            $info = ob_get_clean();

            $start = stripos($info, "<body");
            $end = stripos($info, "</body");

            $phpinfo = substr($info,$start,$end-$start);

            $this->app['cache']->save($this->cachekey, base64_encode($phpinfo), $this->cachetime);
        }

        return new \Twig_Markup($phpinfo, 'UTF-8');
    }
}
