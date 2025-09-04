/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	// config.height = 500
	filebrowserBrowseUrl= 'http://mrtien.com/quantri/ckfinder/ckfinder.html',
    filebrowserImageBrowseUrl= 'http://mrtien.com/quantri/ckfinder/ckfinder.html?type=Images',
    filebrowserFlashBrowseUrl= 'http://mrtien.com/quantri/ckfinder/ckfinder.html?type=Flash',
    filebrowserUploadUrl= 'http://mrtien.com/quantri/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
    filebrowserImageUploadUrl= 'http://mrtien.com/quantri/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
    filebrowserFlashUploadUrl= 'http://mrtien.com/quantri/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
};
