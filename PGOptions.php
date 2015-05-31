<?php
/**
 *@author Sayyed Jamal Ghasemi
 *@author Sayyed Jamal Ghasemi <jamal13647850@gmail.com>
 *@version 1.0.0
 *
 */
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
                update_option($options["id"],$newoptions[$options["id"].'_'.$this->vars['shortname']]);
            endforeach;

    }
    public function reset_options(){
        foreach ($this->vars['Options'] as $options):
            update_option($options["id"],$options["std"]);
        endforeach;
    }
    public function get_options(){
        foreach ($this->vars['Options'] as $options):
            $sval[$options["id"]]=get_option($options["id"],$options["std"]);
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
                            <td style="width:250px;"><label for="<?php echo $optins["id"] ?>"><?php echo $optins["desc"] ?></label>
                            </td>
                            <td>
                                <input name="<?php echo $optins["id"].'_'.$this->vars['shortname'] ?>"
                                       id="<?php echo $optins["id"] ?>" value="<?php echo $sval[$optins["id"]] ?>" type="text" />
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2">
                            <input name="Savesubmit" value='<?php echo __("Save","PG-AdsManager") ?>' type="submit" />
                            <input name="Resetsubmit" value='<?php echo __("Reset","PG-AdsManager") ?>' type="submit" />

                        </td>
                    </tr>
                    <tr>
                        <td style="width:350px;">
                        <a href="http://www.pgsavis.com"><?php echo __('Plugin Name: ','PG-PA').$this->vars['pluginname']; ?></a>
                        <br>
                        <a href="http://www.pgsavis.com"><?php echo __('Plugin Developer: Sayyed Jamal Ghasemi','PG-PA'); ?></a>
                        <br>
                        <a href="http://www.pgsavis.com"><?php echo __('Plugin Website: http://www.pgsavis.com','PG-PA'); ?></a>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    <?php
    }
} 