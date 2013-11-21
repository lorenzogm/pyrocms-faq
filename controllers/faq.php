<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Faq extends Public_Controller
{
    protected $section = 'faq';
    public $namespace = 'faq';
    public $stream = 'faq';

    public function __construct()
    {
        parent::__construct();
        $this->load->driver('streams');
    }

    public function index()
    {
        $params = array(
            'stream'    => 'categories',
            'namespace' => $this->namespace,
            'order_by' => 'ordering_count',
            'sort' => 'asc',
            );
        $faqs = $this->streams->entries->get_entries($params);

        $this->template
        ->set('faqs', $faqs)
        ->build('index');
    }

    public function view($entry_id = 0)
    {
        $entry = $this->streams->entries->get_entry($entry_id, $this->stream, $this->namespace, true);

        $params = array(
            'stream'    => $this->stream,
            'namespace' => $this->namespace,
            'order_by' => 'category',
            'sort' => 'asc',
            );
        $faqs = $this->streams->entries->get_entries($params);

        $params = array(
            'stream'    => 'categories',
            'namespace' => $this->namespace,
            'order_by' => 'ordering_count',
            'sort' => 'asc',
            );
        $categories = $this->streams->entries->get_entries($params);

        $this->template
        ->set('entry', $entry)
        ->set('faqs', $faqs)
        ->set('categories', $categories)
        ->build('view');

    }
}

/* End of file faq.php */
