<?php

namespace XHGui\Controller;

use Slim\App;
use XHGui\AbstractController;
use XHGui\RequestProxy as Request;
use XHGui\ResponseProxy as Response;
use XHGui\Searcher\SearcherInterface;

use Tideways\Xhprof\CachegrindConverter;

class ExportController extends AbstractController
{
    /** @var CachegrindConverter */
    private $converter;

    /**
     * @var SearcherInterface
     */
    protected $searcher;

    public function __construct(App $app, SearcherInterface $searcher)
    {
        parent::__construct($app);
        $this->searcher = $searcher;
        $this->converter = new CachegrindConverter();
    }

    public function cachegrind(Request $request, Response $response)
    {
        $id = $request->get('id');

        $profile = $this->searcher->get($id);
        $output = $this->converter->convertToCachegrind($profile->toArray()['profile']);

        //$response = $this->app->response();
        $response->setHeader('Content-Type', 'application/octet-stream');
        $response->setHeader('Cache-Control', 'public, max-age=60, must-revalidate');
        $response->setHeader('Content-Disposition',
                              sprintf('attachment; filename=cachegrind-%s.out', $id) );
        $response->write($output);
        return $response;
    }
}
