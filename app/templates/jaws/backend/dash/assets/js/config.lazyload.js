/* ============================================================
 * File: config.lazyload.js
 * Configure modules for ocLazyLoader. These are grouped by 
 * vendor libraries. 
 * ============================================================ */

angular.module('jaws')
    .config(['$ocLazyLoadProvider', function($ocLazyLoadProvider) {
        $ocLazyLoadProvider.config({
            debug: true,
            events: true,
            modules: [{
                    name: 'isotope',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/imagesloaded/imagesloaded.pkgd.min.js',
                        _JAWS_PATH_TPL + 'assets/plugins/jquery-isotope/isotope.pkgd.min.js'
                    ]
                }, {
                    name: 'codropsDialogFx',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/codrops-dialogFx/dialogFx.js',
                        _JAWS_PATH_TPL + 'assets/plugins/codrops-dialogFx/dialog.css',
                        _JAWS_PATH_TPL + 'assets/plugins/codrops-dialogFx/dialog-sandra.css'
                    ]
                }, {
                    name: 'metrojs',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/jquery-metrojs/MetroJs.min.js',
                        _JAWS_PATH_TPL + 'assets/plugins/jquery-metrojs/MetroJs.css'
                    ]
                }, {
                    name: 'owlCarousel',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/owl-carousel/owl.carousel.min.js',
                        _JAWS_PATH_TPL + 'assets/plugins/owl-carousel/assets/owl.carousel.css'
                    ]
                }, {
                    name: 'noUiSlider',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/jquery-nouislider/jquery.nouislider.min.js',
                        _JAWS_PATH_TPL + 'assets/plugins/jquery-nouislider/jquery.liblink.js',
                        _JAWS_PATH_TPL + 'assets/plugins/jquery-nouislider/jquery.nouislider.css',
                        _JAWS_PATH_TPL + 'assets/plugins/angular-nouislider/nouislider.js'
                    ]
                }, {
                    name: 'nvd3',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/nvd3/lib/d3.v3.js',
                        _JAWS_PATH_TPL + 'assets/plugins/nvd3/nv.d3.min.js',
                        _JAWS_PATH_TPL + 'assets/plugins/nvd3/src/utils.js',
                        _JAWS_PATH_TPL + 'assets/plugins/nvd3/src/tooltip.js',
                        _JAWS_PATH_TPL + 'assets/plugins/nvd3/src/interactiveLayer.js',
                        _JAWS_PATH_TPL + 'assets/plugins/nvd3/src/models/axis.js',
                        _JAWS_PATH_TPL + 'assets/plugins/nvd3/src/models/line.js',
                        _JAWS_PATH_TPL + 'assets/plugins/nvd3/src/models/lineWithFocusChart.js',
                        _JAWS_PATH_TPL + 'assets/plugins/angular-nvd3/angular-nvd3.js',
                        _JAWS_PATH_TPL + 'assets/plugins/nvd3/nv.d3.min.css'
                    ],
                    serie: true // load in the exact order
                }, {
                    name: 'rickshaw',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/nvd3/lib/d3.v3.js',
                        _JAWS_PATH_TPL + 'assets/plugins/rickshaw/rickshaw.min.js',
                        _JAWS_PATH_TPL + 'assets/plugins/angular-rickshaw/rickshaw.js',
                        _JAWS_PATH_TPL + 'assets/plugins/rickshaw/rickshaw.min.css',
                    ],
                    serie: true
                }, {
                    name: 'sparkline',
                    files: [
                    _JAWS_PATH_TPL + 'assets/plugins/jquery-sparkline/jquery.sparkline.min.js',
                    _JAWS_PATH_TPL + 'assets/plugins/angular-sparkline/angular-sparkline.js'
                    ]
                }, {
                    name: 'mapplic',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/mapplic/js/hammer.js',
                        _JAWS_PATH_TPL + 'assets/plugins/mapplic/js/jquery.mousewheel.js',
                        _JAWS_PATH_TPL + 'assets/plugins/mapplic/js/mapplic.js',
                        _JAWS_PATH_TPL + 'assets/plugins/mapplic/css/mapplic.css'
                    ]
                }, {
                    name: 'skycons',
                    files: [_JAWS_PATH_TPL + 'assets/plugins/skycons/skycons.js']
                }, {
                    name: 'switchery',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/switchery/js/switchery.min.js',
                        _JAWS_PATH_TPL + 'assets/plugins/ng-switchery/ng-switchery.js',
                        _JAWS_PATH_TPL + 'assets/plugins/switchery/css/switchery.min.css',
                    ]
                }, {
                    name: 'menuclipper',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/jquery-menuclipper/jquery.menuclipper.css',
                        _JAWS_PATH_TPL + 'assets/plugins/jquery-menuclipper/jquery.menuclipper.js'
                    ]
                }, {
                    name: 'wysihtml5',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/bootstrap3-wysihtml5/bootstrap3-wysihtml5.min.css',
                        _JAWS_PATH_TPL + 'assets/plugins/bootstrap3-wysihtml5/bootstrap3-wysihtml5.all.min.js'
                    ]
                }, {
                    name: 'stepsForm',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/codrops-stepsform/css/component.css',
                        _JAWS_PATH_TPL + 'assets/plugins/codrops-stepsform/js/stepsForm.js'
                    ]
                }, {
                    name: 'jquery-ui',
                    files: [_JAWS_PATH_TPL + 'assets/plugins/jquery-ui-touch/jquery.ui.touch-punch.min.js']
                }, {
                    name: 'moment',
                    files: [_JAWS_PATH_TPL + 'assets/plugins/moment/moment.min.js',
                        _JAWS_PATH_TPL + 'assets/plugins/moment/moment-with-locales.min.js'
                    ]
                }, {
                    name: 'hammer',
                    files: [_JAWS_PATH_TPL + 'assets/plugins/hammer.min.js']
                }, {
                    name: 'sieve',
                    files: [_JAWS_PATH_TPL + 'assets/plugins/jquery.sieve.min.js']
                }, {
                    name: 'line-icons',
                    files: [_JAWS_PATH_TPL + 'assets/plugins/simple-line-icons/simple-line-icons.css']
                }, {
                    name: 'ionRangeSlider',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/ion-slider/css/ion.rangeSlider.css',
                        _JAWS_PATH_TPL + 'assets/plugins/ion-slider/css/ion.rangeSlider.skinFlat.css',
                        _JAWS_PATH_TPL + 'assets/plugins/ion-slider/js/ion.rangeSlider.min.js'
                    ]
                }, {
                    name: 'navTree',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/angular-bootstrap-nav-tree/abn_tree_directive.js',
                        _JAWS_PATH_TPL + 'assets/plugins/angular-bootstrap-nav-tree/abn_tree.css'
                    ]
                }, {
                    name: 'nestable',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/jquery-nestable/jquery.nestable.css',
                        _JAWS_PATH_TPL + 'assets/plugins/jquery-nestable/jquery.nestable.js',
                        _JAWS_PATH_TPL + 'assets/plugins/angular-nestable/angular-nestable.js'
                    ]
                }, {
                    //https://github.com/angular-ui/ui-select
                    name: 'select',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/bootstrap-select2/select2.css',
                        _JAWS_PATH_TPL + 'assets/plugins/angular-ui-select/select.min.css',
                        _JAWS_PATH_TPL + 'assets/plugins/angular-ui-select/select.min.js'
                    ]
                }, {
                    name: 'datepicker',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/bootstrap-datepicker/css/datepicker3.css',
                        _JAWS_PATH_TPL + 'assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js',
                    ]
                }, {
                    name: 'daterangepicker',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css',
                        _JAWS_PATH_TPL + 'assets/plugins/bootstrap-daterangepicker/daterangepicker.js'
                    ]
                }, {
                    name: 'timepicker',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.css',
                        _JAWS_PATH_TPL + 'assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.js'
                    ]
                }, {
                    name: 'inputMask',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/jquery-inputmask/jquery.inputmask.min.js'
                    ]
                }, {
                    name: 'autonumeric',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/jquery-autonumeric/autoNumeric.js'
                    ]
                }, {
                    name: 'summernote',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/summernote/css/summernote.css',
                        _JAWS_PATH_TPL + 'assets/plugins/summernote/js/summernote.min.js',
                        _JAWS_PATH_TPL + 'assets/plugins/angular-summernote/angular-summernote.min.js'
                    ],
                    serie: true // load in the exact order
                }, {
                    name: 'tagsInput',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/bootstrap-tag/bootstrap-tagsinput.css',
                        _JAWS_PATH_TPL + 'assets/plugins/bootstrap-tag/bootstrap-tagsinput.min.js'
                    ]
                }, {
                    name: 'dropzone',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/dropzone/css/dropzone.css',
                        _JAWS_PATH_TPL + 'assets/plugins/dropzone/dropzone.min.js',
                        _JAWS_PATH_TPL + 'assets/plugins/angular-dropzone/angular-dropzone.js'
                    ]
                }, {
                    name: 'wizard',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/lodash/lodash.min.js',
                        _JAWS_PATH_TPL + 'assets/plugins/angular-wizard/angular-wizard.min.css',
                        _JAWS_PATH_TPL + 'assets/plugins/angular-wizard/angular-wizard.min.js'
                    ]
                }, {
                    name: 'dataTables',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/jquery-datatable/media/css/jquery.dataTables.css',
                        _JAWS_PATH_TPL + 'assets/plugins/jquery-datatable/extensions/FixedColumns/css/dataTables.fixedColumns.min.css',
                        _JAWS_PATH_TPL + 'assets/plugins/datatables-responsive/css/datatables.responsive.css',
                        _JAWS_PATH_TPL + 'assets/plugins/jquery-datatable/media/js/jquery.dataTables.min.js',
                        _JAWS_PATH_TPL + 'assets/plugins/jquery-datatable/extensions/TableTools/js/dataTables.tableTools.min.js',
                        _JAWS_PATH_TPL + 'assets/plugins/jquery-datatable/extensions/Bootstrap/jquery-datatable-bootstrap.js',
                        _JAWS_PATH_TPL + 'assets/plugins/datatables-responsive/js/datatables.responsive.js',
                        _JAWS_PATH_TPL + 'assets/plugins/datatables-responsive/js/lodash.min.js'
                    ],
                    serie: true // load in the exact order
                }, {
                    name: 'google-map',
                    files: [
                        _JAWS_PATH_TPL + 'assets/plugins/angular-google-map-loader/google-map-loader.js',
                        _JAWS_PATH_TPL + 'assets/plugins/angular-google-map-loader/google-maps.js'
                    ]
                }, {
                    name: 'key-enter',
                    files: [
                        _JAWS_PATH_TPL + 'assets/js/directives/key-enter.js',
                    ]
                }
            ]
        });
    }]);