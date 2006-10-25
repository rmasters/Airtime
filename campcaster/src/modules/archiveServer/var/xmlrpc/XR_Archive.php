<?php
/**
 * @author $Author$
 * @version $Revision$
 * @package Campcaster
 * @subpackage ArchiveServer
 */

require_once "../Archive.php";

/**
 * XML-RPC interface for Archive.
 *
 * @author $Author$
 * @version $Revision$
 * @package Campcaster
 * @subpackage ArchiveServer
 *
 */
class XR_Archive extends Archive {

    /**
     * Simple ping method - return strtouppered string
     *
     * @param XMLRPC_struct $input
     */
    function xr_ping($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if (!$ok) {
        	return $r;
        }
        $res = date("Ymd-H:i:s")." Network hub answer: {$r['par']}";
        return new XML_RPC_Response(XML_RPC_encode($res));
    }


    /**
     * @param XMLRPC_struct $input
     */
    function xr_uploadOpen($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if (!$ok) {
        	return $r;
        }
        $res = $this->uploadOpen($r['sessid'], $r['chsum']);
        if (PEAR::isError($res))
            return new XML_RPC_Response(0, 803,
                "xr_uploadOpen: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new XML_RPC_Response(XML_RPC_encode($res));
    }


    /**
     * Check state of file upload
     *
     * @param XMLRPC_struct $input
     */
    function xr_uploadCheck($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if (!$ok) {
        	return $r;
        }
        $res = $this->uploadCheck($r['token']);
        if (PEAR::isError($res))
            return new XML_RPC_Response(0, 803,
                "xr_uploadCheck: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new XML_RPC_Response(XML_RPC_encode($res));
    }


    /**
     * @param XMLRPC_struct $input
     */
    function xr_uploadClose($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if (!$ok) {
        	return $r;
        }
        $res = $this->uploadClose($r['token'], $r['trtype'], $r['pars']);
        if (PEAR::isError($res))
            return new XML_RPC_Response(0, 803,
                "xr_uploadClose: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new XML_RPC_Response(XML_RPC_encode($res));
    }


    /**
     * @param XMLRPC_strcut $input
     */
    function xr_downloadOpen($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if (!$ok) {
        	return $r;
        }
        $res = $this->downloadOpen($r['sessid'], $r['trtype'], $r['pars']);
        if (PEAR::isError($res))
            return new XML_RPC_Response(0, 803,
                "xr_downloadOpen: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new XML_RPC_Response(XML_RPC_encode($res));
    }


    /**
     * @param XMLRPC_struct $input
     */
    function xr_downloadClose($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if (!$ok) {
        	return $r;
        }
        $res = $this->downloadClose($r['token'], $r['trtype']);
        if (PEAR::isError($res))
            return new XML_RPC_Response(0, 803,
                "xr_downloadClose: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new XML_RPC_Response(XML_RPC_encode($res));
    }


    /**
     * @param XMLRPC_struct $input
     */
    function xr_prepareHubInitiatedTransfer($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if (!$ok) {
        	return $r;
        }
        foreach (array('trtype'=>NULL, 'direction'=>'up', 'pars'=>array()) as $k => $dv) {
        	if (!isset($r[$k])) {
        		$r[$k] = $dv;
        	}
        }
        $res = $this->prepareHubInitiatedTransfer(
            $r['target'], $r['trtype'], $r['direction'], $r['pars']);
        if (PEAR::isError($res))
            return new XML_RPC_Response(0, 803,
                "xr_prepareHubInitiatedTransfer: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new XML_RPC_Response(XML_RPC_encode($res));
    }


    /**
     * @param XMLRPC_struct $input
     */
    function xr_listHubInitiatedTransfers($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if (!$ok) {
        	return $r;
        }
        foreach (array('target'=>NULL, 'direction'=>NULL, 'trtok'=>NULL) as $k=>$dv) {
        	if (!isset($r[$k])) {
        		$r[$k] = $dv;
        	}
        }
        $res = $this->listHubInitiatedTransfers(
            $r['target'], $r['direction'], $r['trtok']);
        if (PEAR::isError($res))
            return new XML_RPC_Response(0, 803,
                "xr_listHubInitiatedTransfers: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new XML_RPC_Response(XML_RPC_encode($res));
    }


    /**
     * @param XMLRPC_struct $input
     */
    function xr_setHubInitiatedTransfer($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if (!$ok) {
        	return $r;
        }
        $res = $this->setHubInitiatedTransfer(
            $r['target'], $r['trtok'], $r['state']);
        if (PEAR::isError($res))
            return new XML_RPC_Response(0, 803,
                "xr_setHubInitiatedTransfer: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new XML_RPC_Response(XML_RPC_encode($res));
    }


}

?>