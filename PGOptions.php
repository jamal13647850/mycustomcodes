<?php
/**
 *@author Sayyed Jamal Ghasemi
 *@author Sayyed Jamal Ghasemi <jamal13647850@gmail.com>
 *@version 1.0.0
 *
 */
namespace pgsavis;
class PGOptions {
    private $vars = array();
    public function __construct($param) {
        isset($param['pluginname'])?$this->vars['pluginname']=$param['pluginname']:$this->vars['pluginname']='PG-Plugin';
        isset($param['shortname'])?$this->vars['shortname']=$param['shortname']:$this->vars['shortname']='PG-P';
        $this->vars['Options']=array();
    }
    public function __set($name, $value) {
        $this->vars[$name] = $value ;
    }
    public function __get($name) {
        return $this->vars[$name];
    }
    public function __call($name, $arguments) {

    }
    public function add_options($name,$desc,$id,$type,$std){
            $newfield=array('name'=>$name,'desc'=>$desc,'id'=>$id,'type'=>$type,'std'=>$std);
            array_push($this->vars['Options'],$newfield);
    }
    public function save_options($newoptions){
            foreach ($this->vars['Options'] as $options):
                switch($options['type']){
                    case 'text':
                        update_option($options["id"],$newoptions[$options["id"].'_'.$this->vars['shortname']]);
                        break;
                    case 'textarea':
                        update_option($options["id"],$newoptions[$options["id"].'_'.$this->vars['shortname']]);
                        break;
                    case 'checkbox':
                        update_option($options["id"],$newoptions[$options["id"].'_'.$this->vars['shortname']]);
                        break;
                }
                do_action('pg_after_save_options',$options["id"],$newoptions[$options["id"].'_'.$this->vars['shortname']]);
            endforeach;

    }
    public function reset_options(){
        foreach ($this->vars['Options'] as $options):
            switch($options['type']){
                case 'text':
                    update_option($options["id"],$options["std"]);
                    break;
                case 'textarea':
                    update_option($options["id"],$options["std"]);
                    break;
                case 'checkbox':
                    update_option($options["id"],$options["std"]);
                    break;
            }

        endforeach;
    }
    public function get_options(){
        $sval='';
        foreach ($this->vars['Options'] as $options):
            switch($options['type']){
                case 'text':
                    $sval[$options["id"]]=get_option($options["id"],$options["std"]);
                    break;
                case 'textarea':
                    $sval[$options["id"]]=get_option($options["id"],$options["std"]);
                    break;
                case 'checkbox':
                    $sval[$options["id"]]=get_option($options["id"],$options["std"]);
                    break;
            }
        endforeach;
        $this->vars['options_value']=$sval;
        return $this->vars['options_value'];
    }
    public function create_options_form(){
        if (isset($_POST["Savesubmit"])){
            $this->save_options($_POST);
        }
        if (isset($_POST["Resetsubmit"])){
            $this->reset_options();
        }
        $sval=$this->get_options();
?>
        <div class="pga_panel">
            <form action="" method="post">
                <table class="widefat">
                    <tr>
                        <th colspan="2">تنظیمات</th>
                    </tr>
                    <?php foreach ($this->vars['Options'] as $optins): ?>
                        <tr>
                            <?php echo $this->create_field($optins["name"],$optins["desc"],$optins["id"],$optins["type"],$optins["std"],$sval[$optins["id"]]) ?>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2">
                            <input name="Savesubmit" value='<?php echo __("Save","dr-gmap") ?>' type="submit" />
                            <input name="Resetsubmit" value='<?php echo __("Reset","dr-gmap") ?>' type="submit" />

                        </td>
                    </tr>
                </table>
            </form>
        </div>
    <?php
    }
    private function create_field($name,$desc,$id,$type,$std,$value){
        $return='';
        switch($type){
            case 'header':
                $return="<td style=\"width:250px;\"><h3>".$desc." </h3></td>";
                break;
            case 'text':
                $return="<td style=\"width:250px;\"><label for=\"". $id ."\">".$desc ."</label></td>";
                $newname=$id."_".$this->vars['shortname'];
                $return.=" <td>
                <input style=\"width:250px;\" name=\"". $newname ."\"
                   id=\"". $id ."\" value=\"". $value ."\" type=\"text\" />
                </td>";
                break;
            case 'textarea':
                $return="<td style=\"width:250px;\"><label for=\"". $id ."\">".$desc ."</label></td>";
                $newname=$id."_".$this->vars['shortname'];
                $return.=" <td>
                <textarea name=\"". $newname ."\"
                   id=\"". $id ."\"   />".$value."</textarea>
                </td>";
                break;
            case 'checkbox':
                $return="<td style=\"width:250px;\"><label for=\"". $id ."\">".$desc ."</label></td>";
                $newname=$id."_".$this->vars['shortname'];
                $return.=" <td>
                <input name=\"". $newname ."\"
                   id=\"". $id ."\" ". checked($value,'on',false) ." type=\"checkbox\" />
                </td>";
                break;
            case 'hr':
                $return.=" <td>
                <hr>
                </td>";
                break;
        }
        return $return;
    }
} 