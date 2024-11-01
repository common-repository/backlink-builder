<?php
/*
Plugin Name: IBL Builder
Plugin URI: http://iblbuilder.com/
Version: l3t5.m4k3.7h1s.l00k.4s.c0mpl1c4t3d.4s.p0ss1bl3
Author: Lawliet
Description: Displays IBL Builder outbound links.
*/

function iblbuilder_widget_control()
{
    $options = $newoptions = get_option('iblbuilder_widget');
    if($_POST['iblbuilder-submit'])
    {
        $newoptions['zone_id'] = strip_tags(stripslashes($_POST['iblbuilder-zone_id']));
        $newoptions['title'] = strip_tags(stripslashes($_POST['iblbuilder-title']));
    }

    if($options != $newoptions)
    {
        $options = $newoptions;
        update_option('iblbuilder_widget', $options);
    }

    if($options['title'] == '') $options['title'] = 'Links Title Here';

    ?>
<div>
<label for="iblbuilder-title" style="line-height: 35px; display: block;">
 <?php if($options['zone_id'] == '') { ?><b>To Activate:</b><br>1. Visit IBL Builder and <a target="_blank" href="http://www.iblbuilder.com/">sign up</a> for an account (don't worry, its totally free).
 <br>2. Create a "zone" in your new account.<br>
3. Enter your zone #number into the field below and save.<br>
4. Do link deals on IBL Builder to build up your links.<br><br><?php } ?>
  <b>Title:</b> <input type="text" name="iblbuilder-title" value="<?php echo htmlspecialchars($options['title']); ?>" /><br>
  <b>Zone ID:</b> <input type="text" size="4" name="iblbuilder-zone_id" value="<?php echo htmlspecialchars($options['zone_id']); ?>" />
</label>
<input type="hidden" name="iblbuilder-submit" id="iblbuilder-submit" value="1" />
</div>
    <?php
}

function iblbuilder_widget($args)
{
    $options = get_option('iblbuilder_widget');

    if($options['title'] == '') $options['title'] = 'IBL Links';

    echo $args['before_widget'].$args['before_title'].$options['title'].$args['after_title'];

    if($options['zone_id'] != '')
    {
        $handle = @fopen('http://iblbuilder.net/churn.php?zid='.$options['zone_id'], 'r');
        
        if(!$handle_9dd4e46126)
        {
            $handle = @fopen('http://iblbuilder.com/churn.php?zid='.$options['zone_id'], 'r');
        }
    }

    if($handle)
    {
        $tags = array('{1}', '{2}');
    
        $links = '';
    
        while(!@feof($handle)) $links .= @fread($handle, 8192);
    
        @fclose($handle);
    
          // Process.
    
        $css_length = (int)$links{0}.$links{1}.$links{2};
    
        $template_css = substr($links, 3, $css_length);
    
        if($template_css != '') echo '<style>'.$template_css.'</style>';
    
        $css_length += 3;
    
        $html_length = (int)$links{$css_length}.$links{$css_length + 1}.$links{$css_length + 2};
    
        $template_html = substr($links, $css_length + 3, $html_length);
    
        $html_length += $css_length + 3;
    
        while($links{$html_length} != '')
        {
            $anchor_length = $links{$html_length}.$links{$html_length + 1}.$links{$html_length + 2};
        
              // Get anchor string.
        
            $html_length += 3;
        
            $anchors = substr($links, $html_length, $anchor_length);
        
            $anchors = explode('<', $anchors);
        
            unset($anchors[count($anchors)-1]);
        
        
        
            $html_length += $anchor_length;
        
            $link_length = (int)$links{$html_length}.$links{$html_length + 1}.$links{$html_length + 2}.$links{$html_length + 3};
        
            $link = substr($links, $html_length + 4, $link_length);



            $replacements = array($link, $anchors[array_rand($anchors)]);
        
            echo str_replace($tags, $replacements, $template_html);
        
            $html_length += 4 + $link_length;
        }
    }

    echo $args['after_widget'];
}

function init_iblbuilder()
{
    register_sidebar_widget('IBL Builder', 'iblbuilder_widget');
    register_widget_control('IBL Builder', 'iblbuilder_widget_control');
}

add_action('plugins_loaded', 'init_iblbuilder');

?>
