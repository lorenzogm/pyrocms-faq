<?php defined('BASEPATH') or exit('No direct script access allowed');

class Module_Faq extends Module
{
    public $version = '1.0';
    public $namespace = 'faq';

    public function info()
    {
        $this->load->language($this->namespace.'/'.$this->namespace);

        $info = array(
            'name' => array(
                'en' => 'FAQ',
                ),
            'description' => array(
                'en' => 'FAQ',
                ),
            'frontend' => false,
            'backend' => true,
            'menu' => 'content',
            'roles'    => array(
                'view', 'admin'
                ),
            'sections' => array(
                'faq' => array(
                    'name' => $this->namespace.':label:faq',
                    'uri' => 'admin/'.$this->namespace.'',
                    'shortcuts' => array(
                        array(
                            'name' => $this->namespace.':shortcuts:create_faq',
                            'uri' => 'admin/'.$this->namespace.'/create',
                            'class' => 'add'
                            ),
                        ),
                    ),
                ),
            );

        if ( ! function_exists('group_has_role')) return $info;

            // Access Routes
        if (group_has_role('faq', 'admin'))
        {
            $info['sections']['categories'] = array(
                'name' => $this->namespace.':label:categories',
                'uri' => 'admin/'.$this->namespace.'/categories',
                'shortcuts' => array(
                    array(
                        'name' => $this->namespace.':shortcuts:create_category',
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
    /* custom_data
    -------------------------------------------------- */
    $streams = array('faq', 'categories');

    $fields_assignment = array(
        'faq' => array('title', 'slug', 'category', 'description', 'published'),
        'categories' => array('title', 'slug', 'published'),
        );

    $streams_options = array(
        'faq' => array(
            'view_options' => array('title', 'slug', 'category', 'published'),
            'title_column' => 'title'
            ),
        'categories' => array(
            'view_options' => array('title', 'slug', 'published'),
            'title_column' => 'title'
            ),
        );

    /* dependencies
    -------------------------------------------------- */
    $this->load->driver('streams');
    $this->load->language($this->namespace.'/'.$this->namespace);

    /* uninstall
    -------------------------------------------------- */
    if( ! $this->uninstall())
        return false;

    /* streams
    -------------------------------------------------- */
    $streams_id = $this->add_streams($streams, $streams_options);

    /* folders
    -------------------------------------------------- */
    $array = array();
    $folders = $this->create_folders($array);

    /* fields
    -------------------------------------------------- */
    $fields = array();

    // global
    $fields['title'] = array('name' => $this->lang('title'), 'slug' => 'title', 'type' => 'text', 'unique' => true);
    $fields['slug'] = array('name' => $this->lang('slug'), 'slug' => 'slug', 'type' => 'slug', 'extra' => array('max_length' => 255, 'slug_field' => 'title', 'space_type' => '-'), 'unique' => TRUE);
    $array = array('draft', 'published');
    $fields['published'] = $this->build_choice_field($array, 'published', 'dropdown', 'draft');

    // faqs
    $fields['category'] = array('name' => $this->lang('category'), 'slug' => 'category', 'type' => 'relationship',  'extra' => array('choose_stream' => $streams_id['categories']));
    $fields['description'] = array('name' => $this->lang('description'), 'slug' => 'description', 'type' => 'wysiwyg', 'extra' => array('editor_type' => 'advanced'));

    $this->add_fields($fields);

    /* fields_assignment
    -------------------------------------------------- */
    $this->add_fields_assignment($streams, $fields, $fields_assignment);

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
        // Your Upgrade Logic
    return true;
}

public function help()
{
        // Return a string containing help info
        // You could include a file and return it here.
    return "No documentation has been added for this module.<br />Contact the module developer for assistance.";
}

public function add_streams($streams, $streams_options)
{
    $streams_id = array();
    foreach ($streams as $stream)
    {
        if ( ! $this->streams->streams->add_stream($this->lang($stream), $stream, $this->namespace, $this->namespace.'_', null)) return false;
        else
            $streams_id[$stream] = $this->streams->streams->get_stream($stream, $this->namespace)->id;

        $this->update_stream_options($stream, $streams_options[$stream]);
    }

    return $streams_id;
}

public function update_stream_options($stream, $stream_options)
{
        // Update about, title_column and view options
    $update_data = array(
        'about'        => 'lang:'.$this->namespace.':'.$stream.':about',
        'view_options' => $stream_options['view_options'],
        'title_column' => $stream_options['title_column']
        );
    $this->streams->streams->update_stream($stream, $this->namespace, $update_data);
}

public function build_template($stream = null)
{
    if($stream)
        return array('title_column' => FALSE, 'required' => TRUE, 'unique' => FALSE);
    else
        return array('namespace' => $this->namespace, 'type' => 'text');
}

public function create_folders($array)
{
    $this->load->library('files/files');
    $this->load->model('files/file_folders_m');

    $folder = Files::search($this->namespace);
    if( ! $folder['status'])
        Files::create_folder($parent = '0', $folder_name = $this->namespace);
    $folders[$this->namespace] = $this->file_folders_m->get_by('name', $this->namespace);

    foreach ($array as $label)
    {
        $folder = Files::search($label);
        if( ! $folder['status'])
            Files::create_folder($parent = $folders[$this->namespace]->id, $folder_name = $label);
        $folders[$label] = $this->file_folders_m->get_by('name', $label);
    }

    return $folders;
}

public function add_fields($fields)
{
    foreach($fields AS &$field)
        $field = array_merge($this->build_template(), $field);
    $this->streams->fields->add_fields($fields);
}

public function add_fields_assignment($streams, $fields, $fields_assignment)
{

    foreach ($streams as $stream)
    {
        $assign_data = array();
        foreach($fields_assignment[$stream] as $field_assignment)
            $assign_data[] = array_merge($this->build_template($stream), $fields[$field_assignment]);

        foreach($assign_data as $assign_data_row)
        {
            $field_slug = $assign_data_row['slug'];
            unset($assign_data_row['name']);
            unset($assign_data_row['slug']);
            unset($assign_data_row['type']);
            unset($assign_data_row['extra']);
            $this->streams->fields->assign_field($this->namespace, $stream, $field_slug, $assign_data_row);
        }
    }

}

public function build_choice_field($array, $label, $choice_type, $default_value = 0)
{
    $flag = true;
    $string = '';
    foreach ($array AS $key)
    {
        if($flag)
            $flag = false;
        else
            $string .= "\n";

        $string .= "$key : ".$this->lang($key);
    }

    return array('name' => 'lang:'.$this->namespace.':label:'.$label, 'slug' => $label, 'type' => 'choice', 'extra' => array('choice_data' => $string, 'choice_type' => $choice_type, 'default_value' => $default_value));
}


public function lang($label, $type = 'label')
{
    return 'lang:'.$this->namespace.':'.$type.':'.$label;
}

}