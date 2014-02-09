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
        if(isset($this->current_user))
        {
            if ($this->current_user->group_id == 2)
                $group = ' AND (`visibility` = \'any\' OR `visibility` = \'users\')';
            else
                $group = ' AND (`visibility` = \'any\' OR `visibility` = \'users\' OR `visibility` = \'admins\')';
        }
        else
        {
            $group = ' AND `visibility` = \'any\'';
        }

        $params = array(
            'stream'    => 'categories',
            'namespace' => $this->namespace,
            'where' => '`published` = \'yes\''.$group,
            'order_by' => 'ordering_count',
            'sort' => 'asc',
            );
        $categories = $this->streams->entries->get_entries($params);

        $this->template
        ->set('categories', $categories)
        ->build('index');
    }

    public function category($slug)
    {
        if(isset($this->current_user))
        {
            if ($this->current_user->group_id == 2)
                $group = ' AND (`visibility` = \'any\' OR `visibility` = \'users\')';
            else
                $group = ' AND (`visibility` = \'any\' OR `visibility` = \'users\' OR `visibility` = \'admins\')';
        }
        else
        {
            $group = ' AND `visibility` = \'any\'';
        }

        $params = array(
            'stream' => 'categories',
            'namespace' => $this->namespace,
            'where' => '`slug` = \''.$slug.'\''.$group.' AND `published` = \'yes\'',
            );
        $categories = $this->streams->entries->get_entries($params);

        if(isset($categories['entries'][0]))
        {
            $category = $categories['entries'][0];
        }
        else
        {
            $this->session->set_flashdata('error', 'Página no encontada');
            redirect();
        }

        $params = array(
            'stream' => 'faq',
            'namespace' => $this->namespace,
            'where' => '`category` = \''.$category['id'].'\' AND `published` = \'yes\'',
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
        ->title($category['name'])
        ->set('category', $category)
        ->set('faqs', $faqs)
        ->set('categories', $categories)
        ->build('category');
    }
}

/* End of file faq.php */
