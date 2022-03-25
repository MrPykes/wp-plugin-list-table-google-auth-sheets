<?php
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/template.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';
    require_once ABSPATH . 'wp-admin/includes/screen.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Google_Sheet_Table extends WP_List_Table
{

    /**
     *
     * @since 3.1.0
     * @var array
     */
    protected $array;

    public function __construct($datas)
    {
        $this->array = $datas;

        parent::__construct(array(
            'singular' => 'wp-list-table',
            'plural'   => 'wp-list-tables',
            'ajax'     => true
        ));
    }
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $data = $this->getData($this->array);
        usort($data, array(&$this, 'sort_data'));

        $perPage = 10;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ));

        $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
        $this->process_bulk_action();
    }

    function getData($results)
    {
        $data = [];
        for ($i = 1; $i < count($results); $i++) {
            if (!empty($results[$i])) {
                // if ($action == "delete" && $id != $i) {
                foreach ($results[$i] as $key => $value) {
                    if (isset($_GET['search'])) {
                        if ($results[$i][0] == $_GET['search']) {
                            $data[$i]['id'] = $i;
                            $data[$i][$results[0][$key]] = $value;
                        }
                    } else {
                        $data[$i]['id'] = $i;
                        $data[$i][$results[0][$key]] = $value;
                    }
                }
                // }
            }
        }
        return $data;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            'cb'    => '<input type="checkbox" />',
            'id'          => 'ID',
            'Name'       => 'Name',
            'HTML/CSS'       => 'HTML/CSS',
            'JavaScipt'       => 'JavaScipt',
            'PHP'       => 'PHP',
            'Logic'       => 'Logic',
            'HTML/Bootstrap'       => 'HTML/Bootstrap',
            'Wordpress / Elementor'       => 'Wordpress / Elementor',
            'Laravel'       => 'Laravel',
            'TOTAL'       => 'TOTAL',
            'AVERAGE'       => 'AVERAGE',
        );

        return $columns;
    }


    function column_name($item)
    {
        $item_json = json_decode(json_encode($item), true);
        $actions = array(
            'edit' => sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a>', $_REQUEST['page'], 'edit', $item_json['id']),
            'delete' => sprintf('<a href="?page=%s&action=%s&id=%s">Delete</a>', $_REQUEST['page'], 'delete', $item_json['id']),
        );
        return  sprintf('%s %s', $item_json['Name'], $this->row_actions($actions));
    }

    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array('id');
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     * 
     * Field Name in data Array
     */
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case '<input type="checkbox" />':
            case 'id':
            case 'Name':
            case 'HTML/CSS':
            case 'JavaScipt':
            case 'PHP':
            case 'Logic':
            case 'HTML/Bootstrap':
            case 'Wordpress / Elementor':
            case 'Laravel':
            case 'TOTAL':
            case 'AVERAGE':
                return $item[$column_name];

            default:
                return print_r($item, true);
        }
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'Name'  => array('Name', false),
            'JavaScipt' => array('JavaScipt', false),
            'PHP'   => array('PHP', true)
        );
        return $sortable_columns;
    }
    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data($a, $b)
    {
        // Set defaults
        // $orderby = 'Name';
        // $order = 'asc';

        // // If orderby is set, use this as the sort column
        // if (!empty($_GET['orderby'])) {
        //     $orderby = $_GET['orderby'];
        // }

        // // If order is set use this as the order
        // if (!empty($_GET['order'])) {
        //     $order = $_GET['order'];
        // }


        // $result = strcmp($a[$orderby], $b[$orderby]);

        // if ($order === 'asc') {
        //     return $result;
        // }

        // return -$result;

        // If no sort, default to user_login
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'Name';

        // If no order, default to asc
        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';

        // Determine sort order
        $result = strcmp($a[$orderby], $b[$orderby]);

        // Send final sort direction to usort
        return ($order === 'asc') ? $result : -$result;
    }


    function get_bulk_actions()
    {
        $actions = array(
            'delete'    => __('Delete'),
        );

        return $actions;
    }

    public function process_bulk_action()
    {

        // security check!
        if (isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])) {

            $nonce  = filter_input(INPUT_POST, '_wpnonce');
            $action = 'bulk-' . $this->_args['plural'];

            if (!wp_verify_nonce($nonce, $action))
                wp_die('Nope! Security check failed!');
        }

        $action = $this->current_action();


        switch ($action) {
            case 'delete':
                foreach ($_GET['id'] as $id) {
                    // bulk delete action here
                }

                wp_die('You have deleted this succesfully');
                break;

            case 'edit':
                wp_die('This is the edit page.');
                break;

            default:
                // do nothing or something else
                return;
                break;
        }

        return;
    }

    public function search_box($text, $input_id)
    { ?>
        <p class="search-box">
            <label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
            <input type="search" id="<?php echo $input_id ?>" name="search" value="<?php _admin_search_query(); ?>" />
            <?php submit_button($text, 'button', false, false, array('id' => 'search-submit')); ?>
        </p>
<?php }
}
