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
    function AddSelect_ang_count($name,$options=array(array('value'=>'','text'=>''))){
        $newfield=array('name'=>$name,'type'=>'select_ang_count','options'=>$options);
        array_push($this->vars['metafields'],$newfield);
    }
    function AddSelect_ang_city($name,$options=array(array('value'=>'','text'=>''))){
        $newfield=array('name'=>$name,'type'=>'select_ang_city','options'=>$options);
        array_push($this->vars['metafields'],$newfield);
    }
    function AddMultiSelect($name,$options=array(array('value'=>'','text'=>''))){
        $newfield=array('name'=>$name,'type'=>'multiselect','options'=>$options);
        array_push($this->vars['metafields'],$newfield);

    }
    function Addhr($name){
        $newfield=array('name'=>$name,'type'=>'hr');
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
        ?>
        <div class="salon-maindiv" ng-controller="AddressCtrl" > <table>
            <?php
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
                        $$field['name']!=''?$selected=selected( $$field['name'], $option['value'],false ):$selected=$option['selected'];
                        ?>
                        <option <?php echo $selected ?> value="<?php echo $option['value'] ?>"><?php echo $option['text'] ?></option>
<?php
                    }

                    echo '</select>';

                           echo '</td></tr>';


            }
            if ($field['type']=='select_ang_count'){
                $country=get_countries_by_name($$field['name']);
                foreach($country as $stat){
                    $country= $stat->country;
                    $id=$stat->id;
                }
                if (count($country)){
                  ?>
                    <script>
                            jQuery('html').attr('ng-init',"selectedcountries={ id:"+"'"+"<?php echo $id; ?>"+"',  country:'"+"<?php echo $country; ?>"+"'}")

                    </script>
                    <?php
                }


?>

                <label for="countries" class="settextalign" ><?php echo __('country','dr-gmap') ?></label><br>
                <select  name="<?php echo $field['name']?>" id="id-countries1" required class="form-control drgm-select" ng-model="selectedcountries"
                        ng-options="p.country for p in countries track by p.country" ng-change="changecountry()" data-style-select data-map>
                </select>
               <?php

                echo '</select>';

                echo '</td></tr>';


            }


            if ($field['type']=='select_ang_city'){
                $cities=get_cities_by_name($$field['name']);
                foreach($cities as $stat){
                    $city= $stat->city;
                    $id=$stat->id;
                    $country_id=$stat->country_id;
                }
            if (count($city)){
                ?>
                <script>
                    jQuery('html').attr('ng-init',"selectedcities={ id:"+"'"+"<?php echo $id; ?>"+"',  country_id:'"+"<?php echo $country_id; ?>"+"',  city:'"+"<?php echo $city; ?>"+"'}")

                </script>
            <?php
            }


            ?>

                <label for="cities" class="settextalign"><?php echo __('City','dr-gmap') ?></label><br>
                <select  name="<?php echo $field['name']?>" id="id-cities1" required class="form-control drgm-select" ng-model="selectedcities"
                         ng-options="c.city  for c in city_result track by c.city"  data-style-select data-map>
                </select>
                <?php

                echo '</select>';

                echo '</td></tr>';


            }

            if ($field['type']=='hr'){
                ?>
                <hr>
                <?php
            }
            if ($field['type']=='multiselect'){
                echo  '<tr><td>'.
                    '<select name="'. $field['name'] .'[]" multiple="multiple" id="id-SlectBox" class="SlectBox">';
                foreach($field['options'] as $option){

                    if (in_array($option['value'], $$field['name']))
                    {
                        $mach_value=$option['value'];
                    }
                    else
                    {
                        $mach_value='no_mach_in_this';
                    }
                    $$field['name']!=''?$selected=selected( $mach_value, $option['value'],false ):$selected=$option['selected'];
                    ?>
                    <option <?php echo $selected ?> value="<?php echo $option['value'] ?>"><?php echo $option['text'] ?></option>
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