<?php
/**
 *@author Sayyed Jamal Ghasemi
 *@author Sayyed Jamal Ghasemi <jamal13647850@gmail.com>
 *@version 1.0.0
 *
 */
class PGMetaBox {
    private $vars = array();
    public function __construct($param) {
        $this->vars['metafields']=array();
    }
    public function __set($name, $value) {
        $this->vars[$name] = $value ;
    }
    public function __get($name) {
        return $this->vars[$name];
    }
    public function __call($name, $arguments) {

    }

    function AddText($name,$label,$disabled=false){
        $disabled==true?$ndisabled= 'disabled':$ndisabled= '';
        $newfield=array('name'=>$name,'label'=>$label,'disabled'=>$ndisabled,'type'=>'text');
        array_push($this->vars['metafields'],$newfield);
    }
    function AddRadioButton($name,$value,$label,$disabled=false,$checked=false){
        $disabled==true?$ndisabled= 'disabled':$ndisabled= '';
        $checked==true?$nchecked= 'checked':$nchecked= '';
        $newfield=array('name'=>$name,'value'=>$value,'label'=>$label,'disabled'=>$ndisabled,'checked'=>$nchecked,'type'=>'radio');
        array_push($this->vars['metafields'],$newfield);
    }
    function AddCheckBox($name,$value,$label,$disabled=false,$checked=false){
        $disabled==true?$ndisabled= 'disabled':$ndisabled= '';
        $checked==true?$nchecked= 'checked':$nchecked= '';
        $newfield=array('name'=>$name,'value'=>$value,'label'=>$label,'disabled'=>$ndisabled,'checked'=>$nchecked,'type'=>'checkbox');
        array_push($this->vars['metafields'],$newfield);
    }
    function AddTextArea($name,$label,$disabled=false){
        $disabled==true?$ndisabled= 'disabled':$ndisabled= '';
        $newfield=array('name'=>$name,'label'=>$label,'disabled'=>$ndisabled,'type'=>'textarae');
        array_push($this->vars['metafields'],$newfield);
    }
    function AddFieldSetStart(){
        $newfield=array('type'=>'fieldset');
        array_push($this->vars['metafields'],$newfield);
    }
    function AddFieldSetEnd(){
        $newfield=array('type'=>'fieldset_end');
        array_push($this->vars['metafields'],$newfield);
    }
    function AddSelect($name,$options=array(array('value'=>'','text'=>''))){
        $newfield=array('name'=>$name,'type'=>'select','options'=>$options);
        array_push($this->vars['metafields'],$newfield);

    }
    function main_meta_box() {

        //add_meta_box('pg-ma-meta', __('salon', 'PG-MA'), 'add_salon_meta_box', 'salon', 'normal', 'high');
        add_meta_box($this->vars['meta_box_id'], $this->vars['title'], array($this,'Show_Meta_Box'), $this->vars['screen'], $this->vars['context'],$this->vars['priority']);

    }
    function Show_Meta_Box() {

        global $post;
        foreach($this->vars['metafields'] as $field){
            if (isset($field['name'])){
            $$field['name'] = get_post_meta($post->ID, $field['name'], true);}
        }
        echo '<div class="salon-maindiv"><table>';
        foreach($this->vars['metafields'] as $field){
            if ($field['type']=='text'){
                echo  '<tr><td>
                                <label for="'. $field['name'] .'">' . __($field['label'], 'PG-MA') . '</label>
                                <input type="text"  name="'. $field['name'] .'" value="' . __($$field['name'],'PG-PA') . '" '. $field['disabled'].' />
                            </td></tr>';

            }
            if ($field['type']=='radio'){
                $$field['name']!=''?$checked=checked( $$field['name'], $field['value'],false ):$checked=$field['checked'];
                echo  '<tr><td>
                                <input type="radio"  name="'. $field['name'] .'" value="' . __($field['value'],'PG-PA') . '" '. $field['disabled'].' '.$checked.'/>'.__($field['label']).
                            '</td></tr>';

            }
            if ($field['type']=='checkbox'){
                $$field['name']!=''?$checked=checked( $$field['name'], $field['value'],false ):$checked=$field['checked'];
                echo  '<tr><td>
                                <input type="checkbox"  name="'. $field['name'] .'" value="' . __($field['value'],'PG-PA') . '" '. $field['disabled'].' '.$checked.'/>'.__($field['label']).
                    '</td></tr>';

            }
            if ($field['type']=='textarae'){
                echo  '<tr><td>
                                <label for="'. $field['name'] .'">' . __($field['label'], 'PG-MA') . '</label>
                                 <textarea name="'. $field['name'] .'" id="'. $field['name'] .'" rows="10" '.$field['disabled'].' >
                                    ' . $$field['name'] . '
                                 </textarea>

                            </td></tr>';


            }
            if ($field['type']=='select'){
                echo  '<tr><td>'.
                      '<select name="'. $field['name'] .'">';
                    foreach($field['options'] as $option){
                        print_r($option);
                        ?>
                        <option value="<?php echo $option['value'] ?>"><?php echo $option['text'] ?></option>
<?php
                    }

                    echo '</select>';

                           echo '</td></tr>';


            }
            if ($field['type']=='fieldset'){
                echo  '<fieldset>';
            }
            if ($field['type']=='fieldset_end'){
                echo  '</fieldset>';
            }
        }
        echo '</table></div>';
    }
    function save_meta() {
        global $post;
        if( isset($_POST['post_type']) && ($_POST['post_type'] ==$this->vars['posttype']) ) {
            foreach($this->vars['metafields'] as $field){
                if( isset($_POST[$field['name']]) && $_POST[$field['name']] != get_post_meta($post->ID, $field['name'], true)) {
                    update_post_meta($post->ID, $field['name'], $_POST[$field['name']]);
                }
            }
        }
    }


    function CreateMetaBox($meta_box_id,$title,$callable,$screen,$context='advanced',$priority='default',$posttype){
        $this->vars['meta_box_id']=$meta_box_id;
        $this->vars['title']=$title;
        $this->vars['callable']=$callable;
        $this->vars['screen']=$screen;
        $this->vars['context']=$context;
        $this->vars['priority']=$priority;
        $this->vars['posttype']=$posttype;

        add_action( 'add_meta_boxes', array($this,'main_meta_box') );
        add_action('save_post',  array($this,'save_meta'));
    }

} 