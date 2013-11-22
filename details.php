<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
* FAQ
*
* @package FAQ
* @author Lorenzo García <contact@lorenzo-garcia.com>
* @license http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution-ShareAlike 3.0 Unported
* @link http://lorenzo-garcia.com
*/

class Module_Faq extends Module
{
    public $version = '1.1.0';
    public $name = 'FAQ';
    public $namespace = 'faq';
    public $sections;

    public function __construct()
    {
        // Languages
        $this->load->language($this->namespace.'/faq');

        // Libraries
        $this->load->library($this->namespace.'/streams/streams_details');
        $this->streams_details->set_namespace($this->namespace);
    }

    public function info()
    {
        $sections = array(
            'faq' => array(
                'name' => $this->namespace.':label:faq',
                'uri' => 'admin/'.$this->namespace.'',
                'shortcuts' => array(
                    array(
                        'name' => $this->namespace.':label:create_faq',
                        'uri' => 'admin/'.$this->namespace.'/create',
                        'class' => 'add'
                        ),
                    ),
                ),
            );
        $info = array(
            'name' => array(
                'en' => $this->name,
                ),
            'description' => array(
                'en' => 'Frequently Asked Questions',
                ),
            'skip_xss' => true,
            'frontend' => true,
            'backend' => true,
            'author' => 'Lorenzo García',
            'menu' => 'content',
            'roles'    => array(
                'view', 'admin'
                ),
            'sections' => $sections,
            );

        if ( ! function_exists('group_has_role')) return $info;

        if (group_has_role('faq', 'admin'))
        {
            $info['sections']['categories'] = array(
                'name' => $this->namespace.':label:categories',
                'uri' => 'admin/'.$this->namespace.'/categories',
                'shortcuts' => array(
                    array(
                        'name' => $this->namespace.':label:create_category',
                        'uri' => 'admin/'.$this->namespace.'/categories/create',
                        'class' => 'add'
                        ),
                    ),
                );
        }

        return $info;
    }

    public function install()
    {
        /* uninstall
        -------------------------------------------------- */
        if(!$this->uninstall())
            return false;

        /* custom_data
        -------------------------------------------------- */
        $streams = array('faq', 'categories');

        $field_assignments = array(
            'faq' => array('name', 'slug', 'category', 'description', 'published'),
            'categories' => array('name', 'slug', 'description', 'published'),
            );

        $streams_options = array(
            'faq' => array(
                'view_options' => array('name', 'category', 'published'),
                'title_column' => 'name'
                ),
            'categories' => array(
                'view_options' => array('name', 'description', 'published'),
                'title_column' => 'name'
                ),
            );

        /* streams
        -------------------------------------------------- */
        $streams_id = $this->streams_details->insert_streams($streams, $streams_options);

        /* folders
        -------------------------------------------------- */
        $array = array('products');
        $folders = $this->streams_details->create_folders($array);

        /* fields
        -------------------------------------------------- */
        $fields = $this->fields($streams_id, $folders);

        /* field_assignments
        -------------------------------------------------- */
        $this->streams_details->insert_field_assignments($streams, $fields, $field_assignments);

        return true;
    }

    public function uninstall()
    {
        $this->load->driver('streams');

        $this->streams->utilities->remove_namespace($this->namespace);

        return true;
    }

    public function upgrade($old_version)
    {

        if($old_version < '1.1.0')
        {
            // Change "title" to "name"
            $where = array(
                'field_slug' => 'title',
                'field_namespace' => $this->namespace,
                );
            $update = array(
                'field_name' => $this->streams_details->lang('name'),
                'field_slug' => 'name',
                );
            $this->db->where($where)->update('data_fields', $update);
            $columns = array(
                'title' => array(
                    'name' => 'name',
                    'type' => 'varchar(255)'
                    ),
                );
            $fields = $this->db->list_fields($this->namespace.'_faq');

            if (in_array('title', $fields))
            {
                $this->dbforge->modify_column($this->namespace.'_faq', $columns);
            }

            $fields = $this->db->list_fields($this->namespace.'_categories');
            if (in_array('title', $fields))
            {
                $this->dbforge->modify_column($this->namespace.'_categories', $columns);
            }

            // Add "description" field to "categories" stream
            $field_assignments = $this->streams->fields->get_field_assignments('description', $this->namespace);
            $assignment_exists = false;
            foreach ($field_assignments as $field_assignment)
            {
                if ($field_assignment->stream_slug == 'categories')
                {
                    $assignment_exists = true;
                    break;
                }
            }

            if (!$assignment_exists)
            {
                $streams = array('categories');
                $fields = array();
                $field_assignments = array(
                    'categories' => array('description'),
                    );
                $this->streams_details->insert_field_assignments($streams, $fields, $field_assignments);
            }

            // Update view_options in the "categories" stream
            $update_data['faq'] = array(
                'view_options' => array('name', 'category', 'published'),
                );
            $update_data['categories'] = array(
                'view_options' => array('name', 'description', 'published'),
                );
            $this->streams->streams->update_stream('faq', $this->namespace, $update_data['faq']);
            $this->streams->streams->update_stream('categories', $this->namespace, $update_data['categories']);
        }

        return true;
    }

    public function help()
    {
        // Return a string containing help info
        // You could include a file and return it here.
        return "No documentation has been added for this module.<br />Contact the module developer for assistance.";
    }

    private function fields($streams_id, $folders = array())
    {
        $fields = array();

        $fields['name'] = array('unique' => true);
        $fields['slug'] = array('type' => 'slug', 'extra' => array('slug_field' => 'name', 'space_type' => '-'), 'unique' => true);
        $fields['description'] = array('type' => 'textarea');
        $array = array('yes', 'no');
        $fields['published'] = $this->streams_details->build_choice_field($array, 'published', 'dropdown', 'no', false);

        /* fields:faq
        -------------------------------------------------- */
        $fields['category'] = array('type' => 'relationship',  'extra' => array('choose_stream' => $streams_id['categories']));

        $this->streams_details->insert_fields($fields);

        return $fields;
    }

}

/* End of file details.php */
