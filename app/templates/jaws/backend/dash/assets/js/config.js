/* ============================================================
 * File: config.js
 * Configure routing
 * ============================================================ */

angular.module('jaws')
        .config(['$stateProvider', '$urlRouterProvider', '$ocLazyLoadProvider',

            function ($stateProvider, $urlRouterProvider, $ocLazyLoadProvider) {

                var userBeforeRootScope = _JAWS_USER;
                console.log(userBeforeRootScope);
                $urlRouterProvider
                        .otherwise('/app/home');

                $stateProvider
                        .state('app', {
                            abstract: true,
                            url: "/app",
                            templateUrl: _JAWS_PATH_TPL + "tpl/app.html" + _JAWS_DEV_APPEND,
                            controller: 'AppCtrl',
                            resolve: {
                                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                                        return $ocLazyLoad.load([], {
                                            insertBefore: '#lazyload_placeholder'
                                        })
                                                .then(function () {
                                                    return $ocLazyLoad.load([
                                                        _JAWS_PATH_TPL + 'assets/js/controllers/app.js' + _JAWS_DEV_APPEND
                                                    ]);
                                                });
                                    }],
                                activity: function ($rootScope) {
                                    return true;
                                },
                                defaultSettings: function ($http) {
                                    return $http.get(_JAWS_PATH_API + 'settings.get.dash').then(function (response) {
                                        return response.data;
                                    });
                                }
                            }
                        })

                        .state('app.home', {
                            url: "/home",
                            templateUrl: _JAWS_PATH_TPL + "tpl/home.html" + _JAWS_DEV_APPEND,
                            controller: 'HomeCtrl',
                            resolve: {
                                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                                        return $ocLazyLoad.load([], {
                                            insertBefore: '#lazyload_placeholder'
                                        })
                                                .then(function () {
                                                    return $ocLazyLoad.load([
                                                        _JAWS_PATH_TPL + 'assets/js/controllers/home.js' + _JAWS_DEV_APPEND
                                                    ]);
                                                });
                                    }]
                            }
                        })

                        .state('app.payment', {
                            abstract: true,
                            url: "/payment",
                            templateUrl: _JAWS_PATH_TPL + "tpl/payment.html" + _JAWS_DEV_APPEND
                        })

                        .state('app.payment.edit', {
                            url: "/edit/:p",
                            templateUrl: _JAWS_PATH_TPL + "tpl/payment.edit.html" + _JAWS_DEV_APPEND,
                            controller: 'CtrlPaymentEditt',
                            resolve: {
                                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                                        return $ocLazyLoad.load(['datepicker', 'noUiSlider', 'switchery', 'select', 'key-enter'], {
                                            insertBefore: '#lazyload_placeholder'
                                        })
                                                .then(function () {
                                                    return $ocLazyLoad.load([
                                                        _JAWS_PATH_TPL + 'assets/js/controllers/payment.edit.js' + _JAWS_DEV_APPEND
                                                    ]);
                                                });
                                    }],
                                courses: function ($http) {
                                    //JA-150 starts
                                    var sellerQry = '';
                                    if (userBeforeRootScope.roles.feature_keys.seller) {
                                        // return [];
                                        var sellerId = userBeforeRootScope.sellerid;// Value for seller
                                        var sellerQry = '?sellerId=' + sellerId;
//                                return $http.get(_JAWS_PATH_API + 'seller.programs?sellerId=3').then(function (response) {
//                                    console.log(response.data);
//                                    return response.data;
//                                });
                                    }//else{//JA-150 ENDS


                                    return $http.get(_JAWS_PATH_API + 'course.get.all' + sellerQry).then(function (response) {
                                        return response.data;
                                    });

                                    //}
                                },
                                specializations: function ($http) {
                                    //JA-150 starts
                                    var sellerQry = '';
                                    if (userBeforeRootScope.roles.feature_keys.seller) {
                                        // return [];
                                        var sellerId = userBeforeRootScope.sellerid;// Value for seller
                                        var sellerQry = '?sellerId=' + sellerId;
//                                return $http.get(_JAWS_PATH_API + 'seller.programs?sellerId=3').then(function (response) {
//                                    console.log(response.data);
//                                    return response.data;
//                                });
                                    }//else{//JA-150 ENDS

                                    return $http.get(_JAWS_PATH_API + 'specialization.get.all' + sellerQry).then(function (response) {
                                        return response.data;
                                    });

                                    //}
                                },
                                defaultSettings: function ($http) {

                                    return $http.get(_JAWS_PATH_API + 'settings.get.kform').then(function (response) {
                                        return response.data
                                    });
                                },
                                packageDetails: ['$http', '$stateParams', function ($http, $stateParams) {
                                        if ($stateParams.p) {
                                            return $http({method: "POST", url: _JAWS_PATH_API + 'package.get', params: {package_id: $stateParams.p}}).then(function (response) {
                                                return response.data;
                                            });
                                        } else
                                            return false;
                                    }],
                                bootcampDetails: function ($http) {
                                    //JA-150 starts
                                    var sellerQry = '';
                                    if (userBeforeRootScope.roles.feature_keys.seller) {
                                        // return [];
                                        var sellerId = userBeforeRootScope.sellerid;// Value for seller
                                        var sellerQry = '?sellerId=' + sellerId;
//                                return $http.get(_JAWS_PATH_API + 'seller.programs?sellerId=3').then(function (response) {
//                                    console.log(response.data);
//                                    return response.data;
//                                });
                                    }//else{//JA-150 ENDS

                                    return $http.get(_JAWS_PATH_API + 'bootcamps.get.all' + sellerQry).then(function (response) {
                                        return response.data;
                                    });
                                    // }
                                },
                                programDetails: function ($http) {
                                    var sellerQry = '';
                                    if (userBeforeRootScope.roles.feature_keys.seller) {
                                        // return [];
                                        var sellerId = userBeforeRootScope.sellerid;// Value for seller
                                        var sellerQry = '?sellerId=' + sellerId;
//                                return $http.get(_JAWS_PATH_API + 'seller.programs?sellerId=3').then(function (response) {
//                                    console.log(response.data);
//                                    return response.data;
//                                });
                                    }//else{//JA-150 ENDS


                                    return $http.get(_JAWS_PATH_API + 'programs.get.all' + sellerQry).then(function (response) {
                                        return response.data;
                                    });
                                    //}//JA-150 

                                },
                                fullstackDetails: function ($http) {
                                    var sellerQry = '';
                                    if (userBeforeRootScope.roles.feature_keys.seller) {
                                        // return [];
                                        var sellerId = userBeforeRootScope.sellerid;// Value for seller
                                        var sellerQry = '?sellerId=' + sellerId;
//                                return $http.get(_JAWS_PATH_API + 'seller.programs?sellerId=3').then(function (response) {
//                                    console.log(response.data);
//                                    return response.data;
//                                });
                                    }//else{//JA-150 ENDS

                                    return $http.get(_JAWS_PATH_API + 'fullstack' + sellerQry).then(function (response) {
                                        return response.data;
                                    });
                                    // }
                                }
                            }
                        })

                        .state('app.payment.edittest', {
                            url: "/edit_test/:p",
                            templateUrl: _JAWS_PATH_TPL + "tpl/payment.edit.html" + _JAWS_DEV_APPEND,
                            controller: 'CtrlPaymentEdittest',
                            resolve: {
                                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                                        return $ocLazyLoad.load(['datepicker', 'noUiSlider', 'switchery', 'select', 'key-enter'], {
                                            insertBefore: '#lazyload_placeholder'
                                        })
                                                .then(function () {
                                                    return $ocLazyLoad.load([
                                                        _JAWS_PATH_TPL + 'assets/js/controllers/payment.edittest.js' + _JAWS_DEV_APPEND
                                                    ]);
                                                });
                                    }],
                                courses: function ($http) {
                                    return $http.get(_JAWS_PATH_API + 'course.get.all').then(function (response) {
                                        return response.data;
                                    });
                                },
                                specializations: function ($http) {
                                    return $http.get(_JAWS_PATH_API + 'specialization.get.all').then(function (response) {
                                        return response.data;
                                    });
                                },
                                defaultSettings: function ($http) {
                                    return $http.get(_JAWS_PATH_API + 'settings.get.kform').then(function (response) {
                                        return response.data
                                    });
                                },
                                packageDetails: ['$http', '$stateParams', function ($http, $stateParams) {
                                        if ($stateParams.p) {
                                            return $http({method: "POST", url: _JAWS_PATH_API + 'package.get', params: {package_id: $stateParams.p}}).then(function (response) {
                                                return response.data;
                                            });
                                        } else
                                            return false;
                                    }]
                            }
                        })

                        .state('app.payment.track', {
                            url: "/track",
                            templateUrl: _JAWS_PATH_TPL + "tpl/payment.track.html" + _JAWS_DEV_APPEND,
                            controller: 'CtrlPaymentTrack',
                            resolve: {
                                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                                        return $ocLazyLoad.load(['datepicker', 'noUiSlider', 'switchery', 'select', 'dataTables', 'autonumeric'], {
                                            insertBefore: '#lazyload_placeholder'
                                        })
                                                .then(function () {
                                                    return $ocLazyLoad.load([
                                                        _JAWS_PATH_TPL + 'assets/js/controllers/payment.track.js' + _JAWS_DEV_APPEND
                                                    ]);
                                                });
                                    }],
                                courses: function ($http) {
                                    return $http.get(_JAWS_PATH_API + 'course.get.all?all=1').then(function (response) {
                                        return response.data;
                                    });
                                },
                                defaultSettings: function ($http) {
                                    return $http.get(_JAWS_PATH_API + 'settings.get.aform').then(function (response) {
                                        return response.data;
                                    });
                                },
                                bootcamps: function ($http) {
                                    return $http.get(_JAWS_PATH_API + 'bootcamps.get.all').then(function (response) {
                                        return response.data;
                                    });
                                }
                            }
                        })
                        /*********Start JA-113 : LS Dashboard****** */
                        .state('app.lsdashboard', {
                            url: "/lsdashboard",
                            templateUrl: _JAWS_PATH_TPL + "tpl/ls.dashboard.html" + _JAWS_DEV_APPEND,
                            controller: 'CtrlLsDashboard',
                            resolve: {
                                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                                        return $ocLazyLoad.load(['datepicker', 'noUiSlider', 'switchery', 'select', 'dataTables', 'autonumeric'], {
                                            insertBefore: '#lazyload_placeholder'
                                        })
                                                .then(function () {
                                                    return $ocLazyLoad.load([
                                                        _JAWS_PATH_TPL + 'assets/js/controllers/ls.dashboard.js' + _JAWS_DEV_APPEND
                                                    ]);
                                                });
                                    }]
                            }
                        })
                /*********End JA-113 : LS Dashboard******* */
            }
        ])
        /************Start JA-127 *********** */
        .directive('pagingControl', function () {
            return {
                templateUrl: _JAWS_PATH_TPL + "tpl/pagination.html",
            }
        })
        .factory("paginationChange", function () {
            return {
                pagesChange: function (val, currPage, totalPages) {
                    if (currPage < 6) {
                        return (val <= 10 ? true : false);
                    }
                    if (currPage >= 6 && currPage <= totalPages) {
                        return ((currPage - val <= 5 && val - currPage <= 5) ? true : false);
                    }
                    if (currPage > totalPages - 6) {
                        return (val >= totalPages - 10 ? true : false);
                    }
                },
                pageRang: function (min, max) {
                    var arr = [];
                    for (var i = min; i <= max; i++)
                        arr.push(i);
                    return arr;
                }
            }
        })
        /**Start JA-57 and JA-92 */
        .factory("sliderDateChange", function ($filter) {
            return {
                setSliderDate: function (days, date) {
                    if (date != undefined) {
                        var formatedDate = new Date(date);
                    } else {
                        var formatedDate = new Date();
                    }
                    formatedDate.setDate(formatedDate.getDate() + parseInt(days));
                    var updatedDate = $filter('date')(formatedDate, "MM/dd/yyyy");
                    return updatedDate;
                }
            }
        });
/**End JA-57 and JA-92 */

