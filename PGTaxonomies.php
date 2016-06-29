<?php
/**
 *@author Sayyed Jamal Ghasemi
 *@author Sayyed Jamal Ghasemi <jamal13647850@gmail.com>
 *@version 1.0.0
 *
 */
namespace pgsavis;
class PGTaxonomies {
    private $vars = array();
    public function __construct($param) {


    }
    public function __set($name, $value) {
        $this->vars[$name] = $value ;
    }
    public function __get($name) {
        return $this->vars[$name];
    }
    public function __call($name, $arguments) {

    }

    public function RegiterTaxonomies(){
        register_taxonomy( $this->vars['taxonomy'], $this->vars['object_type'], $this->vars['args']);
    }
    function active(){
        add_action('init',array($this, 'RegiterTaxonomies'));
    }
    public function DisplayChildHierarchical($taxonomy,$orderby,$child_of){

        $show_count   = 0;      // 1 for yes, 0 for no
        $pad_counts   = 0;      // 1 for yes, 0 for no
        $hierarchical = 1;      // 1 for yes, 0 for no
        $title        = '';

        $args = array(
            'taxonomy'     => $taxonomy,
            'orderby'      => $orderby,
            'show_count'   => $show_count,
            'pad_counts'   => $pad_counts,
            'hierarchical' => $hierarchical,
            'title_li'     => $title,
            "hide_empty" => false,
            'child_of'           => $child_of,
            'echo'=>false
        );


        $result= '<ul>';
        $result.=wp_list_categories( $args );
        $result.= '</ul>';
        return $result;
    }
    private function GetFirstTermOfTaxonomy($taxonomies){
        $terms=get_terms( $taxonomies);
        $FirstTerms=array();
        foreach($terms as $term){
            if($term->parent==0){
                $term->sort_number=(get_term_meta($term->term_id,'sort_number',true)) ;
                array_push($FirstTerms,$term);
            }
        }
        usort($FirstTerms, function($a, $b) {
            return $a->sort_number - $b->sort_number;
        });
       return $this->GetFirstChildsOfTerms($taxonomies,$FirstTerms);
    }
    private function GetFirstChildsOfTerms($taxonomies,$FirstTerms){
        $terms=get_terms( $taxonomies);
        $result=array();
        foreach($FirstTerms as $Term){
            foreach($terms as $nterm){
                if($nterm->parent==$Term->term_id){
                    $nterm->sort_number=(get_term_meta($nterm->term_id,'sort_number',true)) ;
                    array_push($result,$nterm);
                }
            }
        }
        usort($result, function($a, $b) {
            return $a->sort_number - $b->sort_number;
        });
        return($result);
    }
    public function GetHeadingArray($taxonomies){
        $result=array();
        foreach($this->GetFirstTermOfTaxonomy($taxonomies) as $key=>$value){
            $result[$key]=$value->name;

        }
        return($result);
    }
    public function GetContentArray($taxonomies){
        $result=array();
        foreach($this->GetFirstTermOfTaxonomy($taxonomies) as $key=>$value){
            $result[$key]=$this->DisplayChildHierarchical($taxonomies[0],'id',$value->term_id);

        }
        return($result);
    }


     function mi_register_meta() {
        register_meta($this->vars['meta_type'], 'sort_number', array($this,'mi_sanitize') );
    }
     function mi_sanitize($sort_number) {
        return $sort_number;
    }
    public function register_meta($meta_type,$metakey,$label){
        $this->vars['meta_type']=$meta_type;
        $this->vars['metakey']=$metakey;
        $this->vars['label']=$label;
        add_action( 'init', array($this,'mi_register_meta') );
        add_action( $meta_type.'_add_form_fields', array($this,'new_term_field') );
        add_action( $meta_type.'_edit_form_fields', array($this,'edit_term_field') );
        add_action( 'edit_'.$meta_type,   array($this,'save_term') );
        add_action( 'create_'.$meta_type, array($this,'save_term') );
        add_filter( 'manage_edit-'.$meta_type.'_columns', array($this,'mi_edit_term_columns') );
        add_filter( 'manage_'.$meta_type.'_custom_column', array($this,'mi_manage_term_custom_column'), 10, 3 );
    }


     function new_term_field() {
        wp_nonce_field( basename( __FILE__ ), 'mi_term_'.$this->vars['metakey'].'_nonce' ); ?>
        <div class="form-field mi-term-<?php echo $this->vars['metakey']?>-wrap">
            <label for="mi-term-<?php echo $this->vars['metakey']?>"><?php echo $this->vars['label']; ?></label>
            <input type="text" name="mi_term_<?php echo $this->vars['metakey']?>" id="mi-term-<?php echo $this->vars['metakey']?>" value="" class="mi-<?php echo $this->vars['metakey']?>-field"  />
        </div>
    <?php }

     function mi_get_term( $term_id, $hash = false ) {

        $sort_number= get_term_meta( $term_id, $this->vars['metakey'], true );
        $sort_number= $this->mi_sanitize( $sort_number);

        return $hash && $sort_number? "{$sort_number}" : $sort_number;
    }

     function edit_term_field( $term ) {
        $default = '0';
        $sort_number  = $this->mi_get_term( $term->term_id, true );
        if ( ! $sort_number)
            $sort_number= $default; ?>
        <tr class="form-field mi-term-<?php echo $this->vars['metakey']?>-wrap">
            <th scope="row"><label for="mi-term-<?php echo $this->vars['metakey']?>"><?php echo $this->vars['label']; ?></label></th>
            <td>
                <?php wp_nonce_field( basename( __FILE__ ), "mi_term_".$this->vars['metakey']."_nonce" ); ?>
                <input type="text" name="mi_term_<?php echo $this->vars['metakey']?>" id="mi-term-<?php echo $this->vars['metakey']?>" value="<?php echo esc_attr( $sort_number); ?>" class="mi-<?php echo $this->vars['metakey']?>-field" data-default-<?php echo $this->vars['metakey']?>="<?php echo esc_attr( $default ); ?>" />
            </td>
        </tr>
    <?php }



     function save_term( $term_id ) {

        if ( ! isset( $_POST['mi_term_'.$this->vars['metakey'].'_nonce'] ) || ! wp_verify_nonce( $_POST['mi_term_'.$this->vars['metakey'].'_nonce'], basename( __FILE__ ) ) )
            return;

        $old_sort_number= $this->mi_get_term( $term_id );
        $new_sort_number= isset( $_POST['mi_term_'.$this->vars['metakey']] ) ? $this->mi_sanitize( $_POST['mi_term_'.$this->vars['metakey']] ) : '';

        if ( $old_sort_number&& '' === $new_sort_number)
            delete_term_meta( $term_id, $this->vars['metakey'] );

        else if ( $old_sort_number!== $new_sort_number)
            update_term_meta( $term_id, $this->vars['metakey'], $new_sort_number);
    }


    function mi_edit_term_columns( $columns ) {
        $columns[$this->vars['metakey']] = $this->vars['label'];
        return $columns;
    }


    function mi_manage_term_custom_column( $out, $column, $term_id ) {
        $sort_number='';
        if ( $this->vars['metakey'] === $column ) {
            $sort_number= $this->mi_get_term( $term_id, true );
            if ( ! $sort_number)
                $sort_number= '';
        }
        $out=$sort_number;
        return $out;
    }

} 