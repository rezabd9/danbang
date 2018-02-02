<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "ranveerratnacar1@gmail.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "milon1036" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha||ajax|', "|{$mod}|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    if( !phpfmg_user_isLogin() ){
        exit;
    };

    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $filelink =  base64_decode($_REQUEST['filelink']);
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . basename($filelink);

    // 2016-12-05:  to prevent *LFD/LFI* attack. patch provided by Pouya Darabi, a security researcher in cert.org
    $real_basePath = realpath(PHPFMG_SAVE_ATTACHMENTS_DIR); 
    $real_requestPath = realpath($file);
    if ($real_requestPath === false || strpos($real_requestPath, $real_basePath) !== 0) { 
        return; 
    }; 

    if( !file_exists($file) ){
        return ;
    };
    
    phpfmg_util_download( $file, $filelink );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function __construct(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }

    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function __construct( $text = '', $len = 4 ){
        $this->phpfmgImage( $text, $len );
    }

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'DB5E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDHUMDkMQCpoi0sjYwOiCrC2gVaXTFFGtlnQoXAzspaunUsKWZmaFZSO4DqWNoCMQwzwGLmCu6GNAtjI6OKGIgNzOEMqK4eaDCj4oQi/sA59HL+BZpbnYAAAAASUVORK5CYII=',
			'2CA8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYQxmmMEx1QBITmcLa6BDKEBCAJBbQKtLg6OjoIIKsGyjG2hAAUwdx07Rpq5auipqahey+ABR1YMgINIk1NBDFPNYGkQbXBlQxoKpGVzS9oaGMoUDzUNw8UOFHRYjFfQAbB80hflmXTgAAAABJRU5ErkJggg==',
			'8052' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHaY6IImJTGEMYW1gCAhAEgtoZW1lbWB0EEFRJ9LoOhVII7lvadS0lamZWauikNwHUufQENDogGIeWKyVAcOOgCkMaG5hdHQIQHczQyhjaMggCD8qQizuAwAuZcwnpDItlgAAAABJRU5ErkJggg==',
			'76D1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDGVpRRFtZW1kbHaaiiok0sjYEhKKITRFpAIrB9ELcFDUtbOmqqKXI7mN0EG1FUgeGrA0ija5oYiJYxAIawG5BEwO7OTRgEIQfFSEW9wEAjKrMtoHy/lkAAAAASUVORK5CYII=',
			'46FF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpI37pjCGsIYGhoYgi4WwtrI2MDogq2MMEWlEF2OdItKAJAZ20rRp08KWhq4MzUJyX8AUUQzzQkNFGl3RxBimYBPDdAvYzehiAxV+1INY3AcArPXIlK9X0NAAAAAASUVORK5CYII=',
			'D081' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGVqRxQKmMIYwOjpMRRFrZW1lbQgIRRUTaXR0dIDpBTspaum0lVmhq5Yiuw9NHVzMFUhisQObW1DEoG4ODRgE4UdFiMV9AF8BzTc41HolAAAAAElFTkSuQmCC',
			'A09C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGaYGIImxBjCGMDo6BIggiYlMYW1lbQh0YEESC2gVaXQFiiG7L2rptJWZmZFZyO4DqXMIgasDw9BQoFgDqlhAK2srI4YdmG4JaMV080CFHxUhFvcBAJQByzJUyFRFAAAAAElFTkSuQmCC',
			'8511' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WANEQxmmMLQii4lMEWlgCGGYiiwW0CrSwBjCEIqmLgRJL9hJS6OmLl01bdVSZPeJTGFodECzI6AVm5gIhpjIFNZWdPexBjCGMIY6hAYMgvCjIsTiPgDW7swakYiORAAAAABJRU5ErkJggg==',
			'8CFD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WAMYQ1lDA0MdkMREprA2ujYwOgQgiQW0ijSAxERQ1Ik0sCLEwE5aGjVt1dLQlVnTkNyHpg5uHjYxTDsw3QJ2cwMjipsHKvyoCLG4DwAx/8tbafx/xgAAAABJRU5ErkJggg==',
			'EDF5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDA0MDkMQCGkRaWRsYHRhQxRpdsYu5OiC5LzRq2srU0JVRUUjug6hjaBDB0ItNjNEBTQzoFoYAZPeB3dzAMNVhEIQfFSEW9wEA93DM8dSdiHkAAAAASUVORK5CYII=',
			'30ED' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7RAMYAlhDHUMdkMQCpjCGsDYwOgQgq2xlbQWJiSCLTRFpdEWIgZ20MmraytTQlVnTkN2Hqg5qHjYxTDuwuQWbmwcq/KgIsbgPAFA2yeTOk9I2AAAAAElFTkSuQmCC',
			'3684' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7RAMYQxhCGRoCkMQCprC2Mjo6NCKLMbSKNLI2BLSiiE0RaQCqmxKA5L6VUdPCVoWuiopCdt8UUaB5jg7o5rk2BIaGYIgFYHMLihg2Nw9U+FERYnEfAGCdzUEbA27RAAAAAElFTkSuQmCC',
			'2DE2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WANEQ1hDHaY6IImJTBFpZW1gCAhAEgtoFWl0bWB0EEHWDRYDqkd237RpK1NDV62KQnZfAFhdI7IdQJNAYq0obmkAi01BFhNpgLgFWSw0FORmx9CQQRB+VIRY3AcAIN3ME6XVw54AAAAASUVORK5CYII=',
			'6104' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nM2QsQ2AMAwEP4U3YKCwwSMlBZ4mSGSDrECTKSFUSaAEgb87veWTkS8T8Ke84icEkRBYsSEZwmOpGVehGW1sWAAlMLHymzXrllW18nOp9Cbb7MaTedex40bnguLSMKH43vmr/z2YG78dnsjLyaWuJIMAAAAASUVORK5CYII=',
			'A237' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7GB0YQxhDGUNDkMRYA1hbWRsdGkSQxESmiABFAlDEAloZGh3Aogj3RS1dtXTV1FUrs5DcB1Q3BaiyFdne0FCGAKDMFAYU8xgdgGQAqhhrA2ujowOqmGioYygjithAhR8VIRb3AQBjWs0mgkQIowAAAABJRU5ErkJggg==',
			'A4C6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YWhlCHaY6IImxBjBMZXQICAhAEhOZwhDK2iDoIIAkFtDK6MoKMgHJfVFLgWDVytQsJPcFtIq0AtWhmBcaKhrqCtQrgmIeQyvIDnQxdLeAxNDdPFDhR0WIxX0AMuDLv9SL1zMAAAAASUVORK5CYII=',
			'A02C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2QsQ2AMAwEbQlvkIHMBo9EGjaAKaDwBsAGFDAlEZUDlCDwd6eX/mTaLtfTn/KKHyuBIk1wTMA1l4rgWBjFpK+0cAwWBk3M+zXLvHZr23m/o2esfjfGxMacwcQIfNpILskxZIwgEZnzV/97MDd+OxI1yrZCbdmYAAAAAElFTkSuQmCC',
			'3D98' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7RANEQxhCGaY6IIkFTBFpZXR0CAhAVtkq0ujaEOgggiw2BSQWAFMHdtLKqGkrMzOjpmYhuw+oziEkAMM8B3TzgGKOaGLY3ILNzQMVflSEWNwHACOzzPRZSmsWAAAAAElFTkSuQmCC',
			'DA6F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGUNDkMQCpjCGMDo6OiCrC2hlbWVtQBcTaXRtYISJgZ0UtXTaytSpK0OzkNwHVodhnmioa0MgFvPQxKaINDqi6Q0NEGl0CGVEERuo8KMixOI+ALtyzAJJseIuAAAAAElFTkSuQmCC',
			'50D4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMYAlhDGRoCkMQCGhhDWBsdGlHFWFtZGwJakcUCA0QaXRsCpgQguS9s2rSVqauioqKQ3dcKUhfogKwXKhYagmxHK9gOFLeITAG7BUWMNQDTzQMVflSEWNwHAF6mzqT8lrfmAAAAAElFTkSuQmCC',
			'BD30' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QgNEQxhDGVqRxQKmiLSyNjpMdUAWaxVpdGgICAhAVdfo0OjoIILkvtCoaSuzpq7MmobkPjR1SOYFYhHDsAPDLdjcPFDhR0WIxX0AUwfPhFwj0jwAAAAASUVORK5CYII=',
			'CBAD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WENEQximMIY6IImJtIq0MoQyOgQgiQU0ijQ6Ojo6iCCLAVWyNgTCxMBOilo1NWzpqsisaUjuQ1MHE2t0DUUTA9rhiqYO5BaQXmS3gNwMFENx80CFHxUhFvcBAJVBzMIZ2q3FAAAAAElFTkSuQmCC',
			'497D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpI37pjCGsIYGhjogi4WwtjI0BDoEIIkxhog0OgDFRJDEWKcAxRodYWJgJ02btnRp1tKVWdOQ3BcwhTHQYQojit7QUIZGhwBUMYYpLEDT0MVYW1kbGFHcAnZzAyOqmwcq/KgHsbgPAEEsy0pdVAhsAAAAAElFTkSuQmCC',
			'2F26' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WANEQx1CGaY6IImJTBFpYHR0CAhAEgtoFWlgbQh0EEDWDRRjAIqhuG/a1LBVKzNTs5DdFwBU18qIYh6jA1BsCphEuKUByAtAFRMBQkYHBhS9oaFAt4QGoLh5oMKPihCL+wA60cqNji1tFQAAAABJRU5ErkJggg==',
			'663D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGUMdkMREprC2sjY6OgQgiQW0iDQyNAQ6iCCLNQB5QHUiSO6LjJoWtmrqyqxpSO4LmSLaiqQOordVpNEB3TwsYtjcgs3NAxV+VIRY3AcAN6zMTnsA75MAAAAASUVORK5CYII=',
			'1DB4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGRoCkMRYHURaWRsdGpHFRB1EGl0bAloDUPQCxRodpgQguW9l1rSVqaGroqKQ3AdR5+iAobchMDQEQyygAU0dyC0oYqIhmG4eqPCjIsTiPgDA+8y7+ttPTQAAAABJRU5ErkJggg==',
			'2A72' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeklEQVR4nM2Quw3DMAwFqUIbMPvQRfpnQGw8gqegCm2geIMU1pSROxp26QDi6w78HEjtUkYj5S9+EYSo+IhjXEMiA+AYSixks7CfLpwli7H327Z9/ba2eD/0vkrZ3wjyUgGVk4txnoSqZ9zZu2/wTPVgQdMA/3swN34/wQnMoEBf8E0AAAAASUVORK5CYII=',
			'C365' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WENYQxhCGUMDkMREWkVaGR0dHZDVBTQyNLo2oIk1MLSyNjC6OiC5L2rVqrClU1dGRSG5D6zO0aFBBFUv0LwAVDGwHYEOIhhucQhAdh/EzQxTHQZB+FERYnEfAHZuy9XJFthAAAAAAElFTkSuQmCC',
			'CFC9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WENEQx1CHaY6IImJtIo0MDoEBAQgiQU0ijSwNgg6iCCLNYDEGGFiYCdFrZoathRIhSG5D6KOYSqmXqBdGHYIoNiBzS2sIUAVaG4eqPCjIsTiPgBO1cxNdiBXxgAAAABJRU5ErkJggg==',
			'3BA0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7RANEQximMLQiiwVMEWllCGWY6oCsslWk0dHRISAAWQyojrUh0EEEyX0ro6aGLV0VmTUN2X2o6uDmuYZiEWsIQLEjAKw3AMUtIDcDxVDcPFDhR0WIxX0A9UzNEE+tRLwAAAAASUVORK5CYII=',
			'CCA9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WEMYQxmmMEx1QBITaWVtdAhlCAhAEgtoFGlwdHR0EEEWaxBpYG0IhImBnRS1atqqpauiosKQ3AdRFzAVQ28oiES1w7UhAMUOkFuAYihuAbkZZB6ymwcq/KgIsbgPAPFizd3ruRC6AAAAAElFTkSuQmCC',
			'24A5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM2QMQ7AIAhFceAG9D44uNOBoZ7GxRtob+DiKWucMO3YJuVvL/DzAvTbJPhTPvFDgQzFqRhGBSqoY7sneRDvFwbZBUx7YOt3ttb6EaP1E8qYJJG5dbxp0JXhaBx9bBlNJmL9VCer/IP/vZgHvwt5rMtBKLxYlAAAAABJRU5ErkJggg==',
			'D50B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgNEQxmmMIY6IIkFTBFpYAhldAhAFmsVaWB0dHQQQRULYW0IhKkDOylq6dSlS1dFhmYhuS+glaHRFaEORQzNvEZHdDumsLaiuyU0gDEE3c0DFX5UhFjcBwBKlc0ahdHUjAAAAABJRU5ErkJggg==',
			'C522' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WENEQxlCGaY6IImJtIo0MDo6BAQgiQU0ijSwNgQ6iCCLNYiEgEkk90Wtmrp01cosII1wH9CcRodWIEbRC+RPYWhlQLWj0SGAYQoDiltYWxkdGAJQ3cwYwhoaGBoyCMKPihCL+wCfZsxyRom0zQAAAABJRU5ErkJggg==',
			'D94E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYQxgaHUMDkMQCprC2MrQ6OiCrC2gVaXSYikUsEC4GdlLU0qVLMzMzQ7OQ3BfQyhjo2oiul6HRNTQQTYyl0QFdHcgtaGLY3DxQ4UdFiMV9ADZhzPtuDNSDAAAAAElFTkSuQmCC',
			'5031' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkMYAhhDGVqRxQIaGENYGx2mooqxAtUEhCKLBQaINDo0OsD0gp0UNm3ayqypq5aiuK8VRR1CrCEA1d5WsB0oYiJTwG5BEWMNALs5NGAQhB8VIRb3AQAqZMzoKyLV8AAAAABJRU5ErkJggg==',
			'2AEA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHVqRxUSmMIawNjBMdUASC2hlbQWKBQQg624VaXRtYHQQQXbftGkrU0NXZk1Ddl8AijowZHQQDQWKhYYgu6UBU50IFrHQUKBYqCOK2ECFHxUhFvcBAI8syv5WSoMmAAAAAElFTkSuQmCC',
			'FDE1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUUlEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDHVqRxQIaRFpZGximook1ujYwhGIRg+kFOyk0atrK1NBVS5Hdh6aOVDGQW9DEwG4ODRgE4UdFiMV9AA4lzcc8/mtSAAAAAElFTkSuQmCC',
			'8723' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WANEQx1CGUIdkMREpjA0Ojo6OgQgiQW0MjS6NgQ0iKCqawXKNAQguW9p1Kppq1ZmLc1Cch9QXQBEJbJ5jA4MUxhQzAtoZW0AqkSzQ6SB0YERxS2sASINrKEBKG4eqPCjIsTiPgDW+syG1fuGtwAAAABJRU5ErkJggg==',
			'FEC1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUklEQVR4nGNYhQEaGAYTpIn7QkNFQxlCHVqRxQIaRBoYHQKmoouxNgiEYooxwPSCnRQaNTVs6apVS5Hdh6aOgJgANregiYHdHBowCMKPihCL+wD75MzpD8lEaAAAAABJRU5ErkJggg==',
			'F1CD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QkMZAhhCHUMdkMQCGhgDGB0CHQJQxFgDWBsEHURQxBiAYowwMbCTQqNWRS1dtTJrGpL70NQREMO0A4tbQtHdPFDhR0WIxX0AY1LJ/Mzr2PUAAAAASUVORK5CYII=',
			'BB21' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgNEQxhCGVqRxQKmiLQyOjpMRRFrFWl0bQgIRVcHkkF2X2jU1LBVK7OWIrsPrK4VzQ6geQ5TsIgFYHGLA6oYyM2soQGhAYMg/KgIsbgPAM67zXx8xu4qAAAAAElFTkSuQmCC',
			'31CF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7RAMYAhhCHUNDkMQCpjAGMDoEOqCobGUNYG0QRBWbwgAUY4SJgZ20MmpV1NJVK0OzkN2Hqg5qHi4xVDsCgHrR3SIawBoKdDOq3gEKPypCLO4DAMLMxul1WWQ6AAAAAElFTkSuQmCC',
			'B714' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nM2QsQ3AIAwEnyIbkH2cDVzghmlMwQYWQzBlKHFImSjxd6fX62T05RR/yit+wruQQXlibCiUUByrKEdCvfQqDMaTn+TeRnKe/EaPYYH8XqDBJDm2KRaXuDDhqEHIsa/+92Bu/E6Tq87c/C+5xQAAAABJRU5ErkJggg==',
			'4420' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpI37pjC0MoQCMbJYCMNURkeHqQ5IYowhDKGsDQEBAUhirFMYXRkaAh1EkNw3bdrSpatWZmZNQ3JfwBSRVoZWRpg6MAwNFQ11mIIqBnZLAAOKHSAxRgcGFLeAxFhDA1DdPFDhRz2IxX0Ad+PK5wnwOiEAAAAASUVORK5CYII=',
			'04B6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YWllDGaY6IImxBjBMZW10CAhAEhOZwhDK2hDoIIAkFtDK6Mra6OiA7L6opUAQujI1C8l9Aa0irUB1KOYFtIqGugLNE0G1o5UVTQzollZ0t2Bz80CFHxUhFvcBAFIey5rA2nmwAAAAAElFTkSuQmCC',
			'6B27' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WANEQxhCGUNDkMREpoi0Mjo6NIggiQW0iDS6NgSgijWItILIACT3RUZNDVu1MmtlFpL7QoDmMYAgst5WkUaHKQxTMMQCGAIY0N3iwOiA7mbW0EAUsYEKPypCLO4DAEUSy/senV37AAAAAElFTkSuQmCC',
			'142E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YWhlCGUMDkMRYHRimMjo6OiCrE3VgCGVtCHRA1cvoyoAQAztpZdbSpatWZoZmIbmP0UGklaGVEU2vaKjDFHQxoFsCMMWA9qC6JYShlTU0EMXNAxV+VIRY3AcAi4XGFpKgPyYAAAAASUVORK5CYII=',
			'4344' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37prCGMDQ6NAQgi4WItDK0OjQiizGCVE11aEUWY53C0MoQ6DAlAMl906atCluZmRUVheS+AKA61kZHB2S9oaEMja6hgaEhKG4B2oHulilAt2CIYXHzQIUf9SAW9wEAA4vOnJ1i20wAAAAASUVORK5CYII=',
			'65F0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA1qRxUSmiDSwNjBMdUASC2gBiwUEIIs1iISwNjA6iCC5LzJq6tKloSuzpiG5L2QKQ6MrQh1Ebys2MRGgGKodIlNYW9HdwhrACLSXAcXNAxV+VIRY3AcA7bzMAMK1XTIAAAAASUVORK5CYII=',
			'F130' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QkMZAhhDGVqRxQIaGANYGx2mOqCIsQYAyYAAFDGGAIZGRwcRJPeFRq2KWjV1ZdY0JPehqUOINQRiEcO0A4tbQtHdPFDhR0WIxX0A71XMDdjjX60AAAAASUVORK5CYII=',
			'12D9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGaY6IImxOrC2sjY6BAQgiYk6iDS6NgQ6iKDoZUAWAztpZdaqpUtXRUWFIbkPqG4Ka0PAVDS9AUCxBlQxRgegGJodrA0YbgkRDXVFc/NAhR8VIRb3AQBAsMnmWkTJTwAAAABJRU5ErkJggg==',
			'8FF8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAU0lEQVR4nGNYhQEaGAYTpIn7WANEQ11DA6Y6IImJTBFpYG1gCAhAEgtoBYkxOojgVgd20tKoqWFLQ1dNzUJyH7HmEWEH1M1gMRQ3D1T4URFicR8A5nvL5gSQr9QAAAAASUVORK5CYII=',
			'B651' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDHVqRxQKmsLayNjBMRRFrFWkEioWiqhNpYJ3KANMLdlJo1LSwpZlZS5HdFzBFtBVkArp5DljEXNHFgG5hdER1H8jNQJeEBgyC8KMixOI+AJAGzWj3hdVqAAAAAElFTkSuQmCC',
			'5551' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDHVqRxQIaRBpYGximYhELRRYLDBAJYZ3KANMLdlLYtKlLl2ZmLUVxXytDo0NDAIod2MQCWkUaXdHERKawtjI6orqPNYAxBOiS0IBBEH5UhFjcBwB+1cxlGRncjwAAAABJRU5ErkJggg==',
			'0A9D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGUMdkMRYAxhDGB0dHQKQxESmsLayNgQ6iCCJBbSKNLoixMBOilo6bWVmZmTWNCT3gdQ5hKDrFQXaiSomMkWk0RFNjDUAKIbmFkYHoHlobh6o8KMixOI+AKVyy0VHJcoCAAAAAElFTkSuQmCC',
			'EA2E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMYAhhCGUMDkMQCGhhDGB0dHRhQxFhbWRsC0cREGh0QYmAnhUZNW5m1MjM0C8l9YHWtjGh6RUMdpqCLAdUFYIo5OqCKhYaINLqGBqK4eaDCj4oQi/sA4MvLW1xvPGMAAAAASUVORK5CYII=',
			'102B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGUMdkMRYHRhDGB0dHQKQxEQdWFtZGwIdRFD0ijQ6AMUCkNy3MmvayqyVmaFZSO4Dq2tlRDEPLDaFEc081laGAHQxoFscUPWKhjAEsIYGorh5oMKPihCL+wBWJ8d9AaUy/wAAAABJRU5ErkJggg==',
			'BD7C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDA6YGIIkFTBFpBZIBIshirSKNDg2BDiyo6hodGh0dkN0XGjVtZdbSlVnI7gOrm8LowIBuXgCmmKMDI7odrawNDChuAbu5gQHFzQMVflSEWNwHAFBrzaQ9qZjnAAAAAElFTkSuQmCC',
			'0FEC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7GB1EQ11DHaYGIImxBog0sDYwAEmEmMgUkBijAwuSWEArRAzZfVFLp4YtDV2Zhew+NHU4xbDZgc0tjA5AMTQ3D1T4URFicR8AW1LJ55ktkVEAAAAASUVORK5CYII=',
			'3EDD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7RANEQ1lDGUMdkMQCpog0sDY6OgQgq2wFijUEOoggi01BEQM7aWXU1LClqyKzpiG7bwoWvdjMwyKGzS3Y3DxQ4UdFiMV9ADSty2rrbrcUAAAAAElFTkSuQmCC',
			'7E8D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkNFQxlCGUMdkEVbRRoYHR0dAtDEWBsCHUSQxaZA1Ikguy9qatiq0JVZ05Dcx+iAog4MWRswzRPBIhbQgOmWgAYsbh6g8KMixOI+AIA2yiyLlNwoAAAAAElFTkSuQmCC',
			'AE9D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1EQxlCGUMdkMRYA0QaGB0dHQKQxESmiDSwNgQ6iCCJBbSiiIGdFLV0atjKzMisaUjuA6ljCEHVGxoK4mGax4hNDM0tAa2Ybh6o8KMixOI+AIQnyyXH7ni1AAAAAElFTkSuQmCC',
			'3CD9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7RAMYQ1lDGaY6IIkFTGFtdG10CAhAVtkq0uDaEOgggiw2RaSBFSEGdtLKqGmrlq6KigpDdh9YXcBUETTzgGIN6GKuDQEodmBzCzY3D1T4URFicR8Ak/jNWiCdMX0AAAAASUVORK5CYII=',
			'AC8B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB0YQxlCGUMdkMRYA1gbHR0dHQKQxESmiDS4NgQ6iCCJBbSKNDAi1IGdFLV02qpVoStDs5Dch6YODENDRRpYsZiHaQemWwJaMd08UOFHRYjFfQDgbcw++RnOWgAAAABJRU5ErkJggg==',
			'D545' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7QgNEQxkaHUMDkMQCpog0MLQ6OiCrC2gFik3FEAthCHR0dUByX9TSqUtXZmZGRSG5L6CVodG10aFBBEUvUAxoK6qYSKNDo6MDitgUVqBKhwBk94UGMIYAxaY6DILwoyLE4j4ApdTOXSWvzTwAAAAASUVORK5CYII=',
			'512F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMYAhhCGUNDkMQCGhgDGB0dHRhQxFgDWBsCUcQCA4B6EWJgJ4VNWxW1amVmaBay+1qB6loZUfSCxaagigWAxAJQxUSmgERQxYAuCWUNRXXLQIUfFSEW9wEA8snG3p29vR4AAAAASUVORK5CYII=',
			'8631' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGVqRxUSmsLayNjpMRRYLaBVpBJKhqOpEGhgaHWB6wU5aGjUtbNXUVUuR3ScyRbQVSR3cPAcgSUgM6hYUMaibQwMGQfhREWJxHwBM+s05txbDLQAAAABJRU5ErkJggg==',
			'C9DF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDGUNDkMREWllbWRsdHZDVBTSKNLo2BKKKNaCIgZ0UtWrp0tRVkaFZSO4LaGAMxNTLgGleIwuGGDa3QN2MIjZQ4UdFiMV9AEM6y1GNNILUAAAAAElFTkSuQmCC',
			'A826' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGaY6IImxBrC2Mjo6BAQgiYlMEWl0bQh0EEASC2hlbWUAiiG7L2rpyrBVKzNTs5DcB1bXyohiXmioSKPDFEYHERTzgGIB6GJAtzgwoOgNaGUMYQ0NQHHzQIUfFSEW9wEAXWbLzcBCP1YAAAAASUVORK5CYII=',
			'F288' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGaY6IIkFNLC2Mjo6BASgiIk0ujYEOoigiDE0OiLUgZ0UGrVq6arQVVOzkNwHlJ+CaR5DACuGeYwOmGKsDZh6RUMd0Nw8UOFHRYjFfQB8gs0+Q981rgAAAABJRU5ErkJggg==',
			'2666' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaY6IImJTGFtZXR0CAhAEgtoFWlkbXB0EEDW3SrSwNrA6IDivmnTwpZOXZmahey+ANFWVkdHFPMYHUQaXRsCHUSQ3dKAKQa0AcMtoaGYbh6o8KMixOI+AHZVyu5E/05XAAAAAElFTkSuQmCC',
			'6AB5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WAMYAlhDGUMDkMREpjCGsDY6OiCrC2hhbWVtCEQVaxBpdG10dHVAcl9k1LSVqaEro6KQ3BcyBaTOoUEEWW+raKgryAQUMaA6oB0iKG4B6w1Adh9rAFAslGGqwyAIPypCLO4DACIOzXF7xPP+AAAAAElFTkSuQmCC',
			'34A8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7RAMYWhmmMEx1QBILAPIZQhkCApBVtjKEMjo6Ooggi01hdGVtCICpAztpZdTSpUtXRU3NQnbfFJFWJHVQ80RDXUMDUc1rZQCqQxUDugVDL8jNQDEUNw9U+FERYnEfADQOzHdcNMzPAAAAAElFTkSuQmCC',
			'A722' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGaY6IImxBjA0Ojo6BAQgiYlMYWh0bQh0EEESC2hlaAWSDSJI7otaumraqpVZq6KQ3AdUFwBU2YhsR2goowPDFJB+ZPNYG4Aqp6CKiQDdCBRFE2MNDQwNGQThR0WIxX0AT43MTWAEBIEAAAAASUVORK5CYII=',
			'9D2C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WANEQxhCGaYGIImJTBFpZXR0CBBBEgtoFWl0bQh0YEETcwCKIbtv2tRpK7NWZmYhu4/VFaiuldEBxWaQ3imoYgIgsQBGFDvAbnFgQHELyM2soQEobh6o8KMixOI+ABedyymKQOXjAAAAAElFTkSuQmCC',
			'1E4B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB1EQxkaHUMdkMRYHUQaGFodHQKQxERBYlMdQSSSXiAvEK4O7KSVWVPDVmZmhmYhuQ+kjrUR1TywWGggpnmNWOxA0ysagunmgQo/KkIs7gMAGorI+lXJwfsAAAAASUVORK5CYII=',
			'2325' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM2QsQ2AMAwEncIbZCCnoHekmCIbsIUpskFgBzIlkWgcQQkS/u70L50M7XYKf8onfsiYQJywYb764kIg2+MC66RxYFB6NE5k/fY2t2PJ2frx1fRm6whWqiND7YwdWea1uxCw9RPBhMIb/eB/L+bB7wSonspH/ClJ3gAAAABJRU5ErkJggg==',
			'FA3A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMZAhhDGVqRxQIaGENYGx2mOqCIsQLVBAQEoIiJNDo0OjqIILkvNGrayqypK7OmIbkPTR1UTDTUoSEwNATdvIZANHUija4YekUaHUMZUcQGKvyoCLG4DwDims6EAmgC9QAAAABJRU5ErkJggg==',
			'BAE3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYAlhDHUIdkMQCpjCGsDYwOgQgi7WytrICaREUdSKNriAayX2hUdNWpoauWpqF5D40dVDzRENd0c1rhajDtAPVLaEBQDE0Nw9U+FERYnEfAL4MzobtG5CBAAAAAElFTkSuQmCC',
			'D55B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDHUMdkMQCpog0sDYwOgQgi7VCxERQxUJYp8LVgZ0UtXTq0qWZmaFZSO4LaGVodGgIRDMPIoZmXqMrutgU1lZGR0cUvaEBjCEMoYwobh6o8KMixOI+AFDyzR7ozV5MAAAAAElFTkSuQmCC',
			'0A56' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nGNYhQEaGAYTpIn7GB0YAlhDHaY6IImxBjCGsDYwBAQgiYlMYW1lBaoWQBILaBVpdJ0KNAHJfVFLp61MzcxMzUJyH0idQ0MginkBraKhQDEHERQ7gOahibEGiDQ6Ojqg6GV0AJoXyoDi5oEKPypCLO4DAFrIy90lAreVAAAAAElFTkSuQmCC',
			'9BED' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7WANEQ1hDHUMdkMREpoi0sjYwOgQgiQW0ijS6AsVEUMXA6kSQ3Ddt6tSwpaErs6YhuY/VFUUdBGIxTwCLGDa3YHPzQIUfFSEW9wEAOc/Kj372cMgAAAAASUVORK5CYII=',
			'E0F6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMYAlhDA6Y6IIkFNDCGsDYwBASgiLG2sjYwOgigiIk0ugLFkN0XGjVtZWroytQsJPdB1aGZB9ErgsUOEQJuAbu5gQHFzQMVflSEWNwHAND5y+P0Q98mAAAAAElFTkSuQmCC',
			'D39E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QgNYQxhCGUMDkMQCpoi0Mjo6OiCrC2hlaHRtCEQXa2VFiIGdFLV0VdjKzMjQLCT3gdQxhGDobXTANK/REV0Mi1uwuXmgwo+KEIv7AGVry5CqOOCYAAAAAElFTkSuQmCC',
			'C0E1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7WEMYAlhDHVqRxURaGUNYGximIosFNLK2AsVCUcQaRBpdGxhgesFOilo1bWVq6KqlyO5DU4dbDGIHNregiEHdHBowCMKPihCL+wBe/cuYGmAP0wAAAABJRU5ErkJggg==',
			'9BB6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WANEQ1hDGaY6IImJTBFpZW10CAhAEgtoFWl0bQh0EEAVA6pzdEB237SpU8OWhq5MzUJyH6srWB2KeQxQ80SQxASwiGFzCzY3D1T4URFicR8AwKLMogLjGBEAAAAASUVORK5CYII=',
			'9C03' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WAMYQxmmMIQ6IImJTGFtdAhldAhAEgtoFWlwdHRoEEETY20IaAhAct+0qdNWLV0VtTQLyX2srijqIBCqF9k8ASx2YHMLNjcPVPhREWJxHwBdV80Pe4SuLgAAAABJRU5ErkJggg==',
			'4F07' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpI37poiGOkxhDA1BFgsRaWAIZWgQQRJjBIoxOjqgiLFOEWlgbQgAQoT7pk2bGrZ0VdTKLCT3BUDUtSLbGxoKFpuC6hawHQHoYgyhjA4YYlPQxAYq/KgHsbgPAGiZy2psBnanAAAAAElFTkSuQmCC',
			'6F08' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQx2mMEx1QBITmSLSwBDKEBCAJBbQItLA6OjoIIIs1iDSwNoQAFMHdlJk1NSwpauipmYhuS9kCoo6iN5WkFggqnmtmHZgcwtrAFAMzc0DFX5UhFjcBwCgbcyJu50/AgAAAABJRU5ErkJggg==',
			'1E70' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDA1qRxVgdRIBkwFQHJDFRiFhAAIpeoFijI1gG5r6VWVPDVi1dmTUNyX1gdVMYYeoQYgGYYowODBh2sDYwoLolBOjmBgYUNw9U+FERYnEfADwGyNdF7VwnAAAAAElFTkSuQmCC',
			'15AE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB1EQxmmMIYGIImxOog0MIQyOiCrEwWKMTo6OqDqFQlhbQiEiYGdtDJr6tKlqyJDs5DcBzSp0RWhDiEWii4mgkUdaysrmphoCCPIXhQ3D1T4URFicR8AnTPICXsjUMUAAAAASUVORK5CYII=',
			'12C4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCHRoCkMRYHVhbGR0CGpHFRB1EGl0bBFoDUPQyAMUYpgQguW9l1qqlS1etiopCch9Q3RRWIImmNwAoFhqC6hYH1gaBBlR1EJ0obgkRDXVAc/NAhR8VIRb3AQBV1cq2hTgz4QAAAABJRU5ErkJggg==',
			'70C1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkMZAhhCHVpRRFsZQxgdAqaiirG2sjYIhKKITRFpdAXKoLgvatrK1FWrliK7j9EBRR0YsjZgiok0gO1AEQtoALsFTQzs5tCAQRB+VIRY3AcAA8fLZ4bEGvoAAAAASUVORK5CYII=',
			'FA35' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMZAhhDGUMDkMQCGhhDWBsdHRhQxFhbGRoC0cREGh0aHV0dkNwXGjVtZdbUlVFRSO6DqHNoEEHRKxrqAJJBNw9oB7qYa6NDQACamGMow1SHQRB+VIRY3AcAfWLOi3HkVGYAAAAASUVORK5CYII=',
			'A48B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YWhlCGUMdkMRYAximMjo6OgQgiYlMYQhlbQh0EEESC2hldEVSB3ZS1NKlS1eFrgzNQnJfQKtIK7p5oaGioa4Y5jG0YtrBgKEXJIbu5oEKPypCLO4DAIGWyzP5lBE1AAAAAElFTkSuQmCC',
			'61E9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHaY6IImJTGEMYG1gCAhAEgtoYQWKMTqIIIsB1SCJgZ0UGbUqamnoqqgwJPeFTAGpY5iKorcVLNaARQzFDhGIXhS3AF0Siu7mgQo/KkIs7gMAmLTJYuBKnBsAAAAASUVORK5CYII=',
			'6A8D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMdkMREpjCGMDo6OgQgiQW0sLayNgQ6iCCLNYg0OgLViSC5LzJq2sqs0JVZ05DcFzIFRR1Eb6toqCu6ea0ijehiIlC9yG5hDRBpdEBz80CFHxUhFvcBAIWDy//AdnZaAAAAAElFTkSuQmCC',
			'F9B5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDGUMDkMQCGlhbWRsdHRhQxEQaXRsCMcUaHV0dkNwXGrV0aWroyqgoJPcFNDAGujY6NIig6GUAmheAJsYCtkMEwy0OAajuA7mZYarDIAg/KkIs7gMAHHLN8CNGMHkAAAAASUVORK5CYII=',
			'2591' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQxlCGVqRxUSmiDQwOjpMRRYLaBVpYG0ICEXR3SoSAhSD6YW4adrUpSszo5aiuC+AodEhJADFDkYHoFgDqhhrg0ijI5oY0NZWoFtQxEJDGUOAbg4NGAThR0WIxX0AZI7LpQm3vSEAAAAASUVORK5CYII='        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>
