/* ============================================================
 * File: config.js
 * Configure routing
 * ============================================================ */

angular.module('jaws')
    .config(['$stateProvider', '$urlRouterProvider', '$ocLazyLoadProvider',

        function ($stateProvider, $urlRouterProvider, $ocLazyLoadProvider) {
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
                                return $http({ method: "POST", url: _JAWS_PATH_API + 'package.get', params: { package_id: $stateParams.p } }).then(function (response) {
                                    return response.data;
                                });
                            }
                            else return false;
                        }],
                        bootcampDetails: function ($http) {
                            return $http.get(_JAWS_PATH_API + 'bootcamps.get.all').then(function (response) { return response.data; });
                        },
                        programDetails: function ($http) {
                            return $http.get(_JAWS_PATH_API + 'programs.get.all').then(function (response) { return response.data; });
                        },
                        fullstackDetails: function ($http) {
                            return $http.get(_JAWS_PATH_API + 'fullstack').then(function (response) { return response.data; });
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
                                return $http({ method: "POST", url: _JAWS_PATH_API + 'package.get', params: { package_id: $stateParams.p } }).then(function (response) {
                                    return response.data;
                                });
                            }
                            else return false;
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
                            return $http.get(_JAWS_PATH_API + 'bootcamps.get.all').then(function (response) { return response.data; });
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
                        return $ocLazyLoad.load([], {
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
    ]);
