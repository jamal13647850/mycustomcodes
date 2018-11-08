<?php
use medicalinfo\DBManager;
use medicalinfo\HtmlElements;

/**
 *@author Sayyed Jamal Ghasemi
 *@author Sayyed Jamal Ghasemi <jamal13647850@gmail.com>
 *@version 1.0.0
 *
 */
 namespace pgsavis;
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
    function AddCheckBox($name,$value,$label,$disabled=false){
        $disabled==true?$ndisabled= 'disabled':$ndisabled= '';
        $newfield=array('name'=>$name,'value'=>$value,'label'=>$label,'disabled'=>$ndisabled,'type'=>'checkbox');
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
    function AddSelect($name,$label,$options=array(array('value'=>'','text'=>''))){
        $newfield=array('name'=>$name,'label'=>$label,'type'=>'select','options'=>$options);
        array_push($this->vars['metafields'],$newfield);

    }
    function AddMultiSelect($name,$label,$options=array(array('value'=>'','text'=>''))){
        $newfield=array('name'=>$name,'label'=>$label,'type'=>'multiselect','options'=>$options);
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
                                <input type="text"  name="'. $field['name'] .'" value="' . get_post_meta($post->ID, $field['name'], true) . '" '. $field['disabled'].' />
                            </td></tr>';

            }
            if ($field['type']=='radio'){
                $$field['name']!=''?$checked=checked( $$field['name'], $field['value'],false ):$checked=$field['checked'];
                echo  '<tr><td>
                                <input type="radio"  name="'. $field['name'] .'" value="' . $field['value'] . '" '. $field['disabled'].' '.$checked.'/>'.__($field['label']).

                    '</td></tr>';


            }
            if ($field['type']=='checkbox'){
                $field['value']=get_post_meta($post->ID, $field['name'], true);;

                $result="<tr><td>";
                $result.="<input type=\"checkbox\"  name=\"". $field['name'] ."\" value=\"" . 'on' . "\" ". $field['disabled']." ".checked( 'on', $field['value'],false )."/>".__($field['label']);
                $result.="</td></tr>";
                echo  $result;

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
                $result="<tr><td>";
                $result.="<label for=\"". $field['name'] ."\">" .$field['label'] . "</label>";
                $result.='<select name="'. $field['name'] .'" id="'.$field['name'].'">';
                $value=get_post_meta($post->ID, $field['name'], true);
                foreach($field['options'] as $option){
                    $result.="<option ".selected( $value, $option['value'],false )." value=\"".$option['value']."\">".$option['text']."</option>";
                }
                $result.='</select></td></tr>';
                echo $result;

            }
            if ($field['type']=='multiselect'){
                $value=get_post_meta($post->ID, $field['name'], true);
                $result="<tr><td>";
                $result.="<label for=\"". $field['name'] ."\">" .$field['label'] . "</label>";
                $result.="<select class=\"chosen-select\" name=\"". $field['name'] ."[]\" multiple=\"multiple\">";
                foreach($field['options'] as $option){
                    if (is_array($value) && in_array($option['value'], $value)) {
                        $mach_value=$option['value'];
                    }
                    else {
                        $mach_value='no_mach_in_this';
                    }
                    $result.="<option ".selected( $mach_value, $option['value'],false ) ." value=\"". $option['value']. "\">". $option['text'] ."</option>";

                }
                $result.= '</select>';
                $result.= '</td></tr>';
                echo $result;

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
                if( isset($_POST[$field['name']]) && $_POST[$field['name']] != get_post_meta($post->ID, $field['name'], true) && $_POST[$field['type']]!='multiselect' ) {
                    update_post_meta($post->ID, $field['name'], $_POST[$field['name']]);
                }
                elseif( isset($_POST[$field['name']]) && $_POST[$field['name']] != get_post_meta($post->ID, $field['name'], true) && $_POST[$field['type']]=='multiselect' ){

                    update_post_meta($post->ID, $field['name'], implode("*",$_POST[$field['name']]));
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