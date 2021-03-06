<?php
/** Freesewing\Channels\Core\Channel */
namespace Freesewing\Channels\Core;

use Freesewing\Context;

/**
 * Abstract class for channels.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
abstract class Channel
{
    /** @var array $config The channel configuration */
    protected $config = array();

    /**
     * Constructor loads the Yaml config into the config property
     *
     * Note that by default, a channel has no config file.
     * This is just here for extensibility
     *
     * @throws InvalidArgument if the Yaml file is not valid
     */
    public function __construct()
    {
        $file = \Freesewing\Utils::getClassDir($this).'/config.yml';
        if(is_readable($file)) $this->config = \Freesewing\Yamlr::loadYamlFile($file);
    }

    /**
     * Channel designer gets the final say before we send a response
     *
     * Before we send a response, you get a chance to decided 
     * whether you are ok with it or not.
     *
     * This is also the place to add headers to the response.
     *
     * @param \Freesewing\Context $context The context object
     *
     * @return bool true Always true in this case
     */
    abstract public function isValidResponse(Context $context);

    /**
     * Channel designer should implement access control
     *
     * You may not want to make your channel publically accessible.
     * You can limit access here in whatever way you like.
     * You have access to the entire context to decide what to do.
     *
     * @param \Freesewing\Context $context The context object
     *
     * @return bool true Always true in this case
     */
    abstract public function isValidRequest(Context $context);

    /**
     * What to do when a request is considered to be invalid
     *
     * If you return false in isValidRequest() then we need to do
     * something with the ongoing request. Since you decided it's
     * no good, you get to decide what to do with it.
     *
     * By default, we redirect to the documentation.
     *
     * @param \Freesewing\Context $context The context object
     *
     * @return void Redirect to the documentation
     */
    public function handleInvalidRequest($context)
    {
        // Call cleanup before bailing out
        $this->cleanUp();

        // Redirect to docs
        $response = new \Freesewing\Response();
        $response->addHeader('redirect', "Location: https://docs.freesewing.org/");
        $context->setResponse($response);
    }

    /**
     * What to do when a response is considered to be invalid
     *
     * If you return false in isValidResponse() then we need to do
     * something with the response. Since you decided it's
     * no good, you get to decide what to do with it.
     *
     * By default, we redirect to the documentation.
     *
     * @param \Freesewing\Context $context The context object
     *
     * @return void Redirect to the documentation
     */
    public function handleInvalidResponse($context)
    {
        // Call cleanup before bailing out
        $this->cleanUp();

        // Redirect to docs
        $response = new \Freesewing\Response();
        $response->addHeader('redirect', "Location: http://docs.freesewing.org/");
        $context->setResponse($response);
    }

    /**
     * Clean up does nothing
     *
     * By default, there's nothing to clean up. But if your channel
     * is logging to a database (for example), you could close that
     * database connection here.
     *
     * @return void Nothing
     */
    public function cleanUp()
    {
    }

    /**
     * Turn input into model measurements that we understand.
     *
     * Each channel must implement this function.
     *
     * @param \Freesewing\Request $request The request object
     * @param \Freesewing\Patterns\* $pattern The pattern object
     *
     * @return array The model measurements
     */
    abstract public function standardizeModelMeasurements($request, $pattern);

    /**
     * Turn input into pattern options that we understand.
     *
     * Each channel must implement this function.
     *
     * @param \Freesewing\Request $request The request object
     * @param \Freesewing\Patterns\* $pattern The pattern object
     *
     * @return array The pattern options
     */
    abstract public function standardizePatternOptions($request, $pattern);
    
}
