<?php

// No direct access.
defined('_JEXEC') or die;

/**
 * Email cloack wavelet class.
 *
 * @package		Joomla.Plugin
 * @subpackage	Content.wavelet
 */
class plgContentWavelet extends JPlugin {

    public function onContentPrepare($context, &$row, &$params, $page = 0) {
        // Don't run this plugin when the content is being indexed
        if ($context == 'com_finder.indexer') {
            return true;
        }

        if (is_object($row)) {
            return $this->_waveletDo($row->text, $params);
        }

        return $this->_waveletDo($row, $params);
    }

    protected function _waveletDo(&$text, &$params) {

        $preps = $this->params->get('preps', 1);
        $units = $this->params->get('units', 1);
        $titles = $this->params->get('titles', 1);
        $num_groups = $this->params->get('num_groups', 1);
        $deg_perc = $this->params->get('deg_perc', 1);

        $pattern = array();
        $replace = array();

        //Handle prepositions - generate patterns
        if ($preps == 1) {
            $PREPS = array("k", "s", "v", "z", "o", "u", "i", "a", "do", "ke", "ku", "na", "od", "po", "se", "ve", "za", "ze", "že", "až", "oč", "už", "K", "S", "V", "Z", "O", "U", "I", "A", "Do", "Ke", "Ku", "Na", "Od", "Po", "Se", "Ve", "Za", "Ze", "Že", "Až", "Oč", "Už");
            for ($i = 0; $i < count($PREPS); $i++) {
                $pattern[] = "/([ (\.>])$PREPS[$i] /";
                $replace[] = '\1' . $PREPS[$i] . '&nbsp;';
            }
        }

        if ($units == 1) {
            //Handle units - generate patterns
            $UNITS = array("cl", "cm", "dl", "dm", "g", "hl", "sk", "kg", "km", "ks", "l", "m", "mg", "ml", "mm", "t", "kč", "Kč");
            for ($i = 0; $i < count($UNITS); $i++) {
                $pattern[] = "/ $UNITS[$i]([ )\.<])/";
                $replace[] = '&nbsp;' . $UNITS[$i] . '\1';
            }
        }

        if ($titles == 1) {
            //Handle titles - generate patterns
            //before name
            $TITLES = array("Doc", "Dr", "gen", "Ing", "JUDr", "kpt", "Mgr", "mjr", "MUDr", "MVDr", "p", "PaeDr", "PhDr", "pí", "ppor", "pplk", "Prof", "RNDr", "sl");
            for ($i = 0; $i < count($TITLES); $i++) {
                $pattern[] = "/([ (\.>])$TITLES[$i]\. /";
                $replace[] = '\1' . $TITLES[$i] . '.&nbsp;';
            }
            //after name
            $TITLES = array("DiS\.", "dis\.", "Dis\.", "dr\. h\. c\.", "dr\.h\.c\.", "DrSc\.", "CSc\.", "Th\.D\.", "Ph\.D\.");
            for ($i = 0; $i < count($TITLES); $i++) {
                $pattern[] = "/([a-zěščřžýáíéúůďňť]) $TITLES[$i]/";
                $replace[] = '\1&nbsp;' . $TITLES[$i];
            }
        }

        if ($num_groups == 1) {
            //Handle number groups - generate patterns
            $pattern[] = "/(?<=[\d+]) (\d+)/";
            $replace[] = '&nbsp;\1';
        }

        if ($deg_perc == 1) {
            //Handle degrees and percents
            $pattern[] = "/(?<=[\d+|[:alpha:]]) ([%|°])/";
            $replace[] = '&nbsp;\1';
        }

        //Real replacement in text
        if (count($pattern) > 0) {
            $text = preg_replace($pattern, $replace, $text);
        }
        //END
        return true;
    }

}
