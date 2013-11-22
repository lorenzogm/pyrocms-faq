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
        ->set('categories', $categories)
        ->build('index');
    }

    public function category($slug)
    {
        $params = array(
            'stream' => 'categories',
            'namespace' => $this->namespace,
            'where' => '`slug` = \''.$slug.'\'',
            );
        $categories = $this->streams->entries->get_entries($params);

        if(isset($categories['entries'][0]))
        {
            $category = $categories['entries'][0];
        }
        else
        {
            return show_404();
        }

        $params = array(
            'stream' => 'faq',
            'namespace' => $this->namespace,
            'where' => '`category` = \''.$category['id'].'\'',
            'order_by' => 'ordering_count',
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
        ->set('category', $category)
        ->set('faqs', $faqs)
        ->set('categories', $categories)
        ->build('category');
    }
}

/* End of file faq.php */
