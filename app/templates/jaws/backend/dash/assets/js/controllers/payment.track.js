'use strict';

/* Controllers */

angular.module('jaws')
    .controller('CtrlPaymentTrack', ['$scope', '$state', 'courses', 'defaultSettings', 'apiSVC', '$sce', '$timeout', '$http', '$window', 'bootcamps','$filter', function ($scope, $state, courses, defaultSettings, apiSVC, $sce, $timeout, $http, $window, bootcamps,$filter) {

        //JA-57 starts
            //edit varaibles
            var installUpdateUrl = _JAWS_PATH + "webapi/backend/dash/edit-installment";
            $scope.instlAction ={};
            $scope.instlAction.edit = false;
            $scope.instlAction.instl = -1;
            $scope.instlAction.instlList = '';
            $scope.max_due_date = (defaultSettings.max_due_date) ? defaultSettings.max_due_date : 45;
            $scope.instls = [];

            $scope.deleteInstl = function(pkg, index){

                if(pkg.instl[index].added){
                    pkg.instl.splice(index, 1);
                }else{
                    if(pkg.instl[index].deleted){
                        pkg.instl[index].discounted = false;
                        pkg.instl[index].edited = 2;

                    }else{

                        pkg.instl[index].edited = 1;
                    }
                }
            };

            $scope.discountInstl = function(pkg, index){

               if(pkg.instl[index].discounted){
                    pkg.instl[index].deleted = false;
                    pkg.instl[index].edited = 3;
                }else{
                    pkg.instl[index].edited = 1;
                }
            };

            $scope.addInstl = function(pkg, index){
                pkg.instl.push({
                     instl_id : '',
                     new_amnt:'',
                     new_duedays : '',
                     new_date : '',
                     edited: 4,
                     added: true,
                     deleted: false,
                     discounted:false,
                     amntAdjst : 0,
                     subsStart:pkg.subsStart,
                     subsEnd:pkg.subsEnd,
                     endDueDays:Number(pkg.instl[1].endDueDays),
                     startDueDays:Number(pkg.startDueDays)
                 });
            };

            $scope.resetInstlAction = function(pkg){

                    pkg.instl.forEach(function (inst, i) {
                        if( pkg.instl[i].added ==true){
                            pkg.instl.splice(i, 1); //remove the new instllment
                        }else{
                            inst.new_amnt = '';
                            inst.new_duedays = '';
                            inst.new_date = '';
                            inst.edited = (inst.pay_date)? 0 :1;
                            inst.deleted = false;
                            inst.discounted = false;
                            inst.added=false;
                        }
                    });

                    $scope.instlAction.edit=false;$scope.instlAction.instl=-1;
                    $scope.instlAction.instlList='';
            };
            $scope.editButtonDisabled = true;
            $scope.updateInstlAmnt = function(pkg,value, index){
                if(value!=''){
                    var totalPrice = Number(pkg.pricing.total);
                    var instlPriceTotal = 0;

                    pkg.instl.forEach(function (inst, i) {
                        inst.amntAdjst = 0;
                        if(inst.pay_date && i < index){
                            instlPriceTotal+= Number(inst.sum);
                        }
                        if(!inst.pay_date && i < index){
                            instlPriceTotal+= Number(inst.new_amnt);
                        }
                        if(i > index){
                            inst.new_amnt = 0;
                        }
                        if(i >= index){
                            instlPriceTotal+= Number(inst.new_amnt);
                        }
                    });
                    pkg.instlSum = 0;
                    var remaingInstlLength = (pkg.instl.length - 1) - index;
                    var priceDiff = totalPrice - instlPriceTotal;
                    if(priceDiff < 0){
                        pkg.extraAmnt = priceDiff;
                    }

                    var perInstlNewPrice = (remaingInstlLength>0 )? (priceDiff/remaingInstlLength) : priceDiff;

                    pkg.instl.forEach(function (inst, i) {
                        if( i > index ){
                            inst.new_amnt = perInstlNewPrice;
                        }
                        if( i == (pkg.instl.length - 1)){
                            if(priceDiff < 0){
                                inst.new_amnt = Number(inst.new_amnt) + Number(priceDiff);
                                inst.amntAdjst = 1;
                            }
                        }
                        if(inst.pay_date){
                            pkg.instlSum+= Number(inst.sum);
                        }else{
                            pkg.instlSum+=Number(inst.new_amnt);
                        }
                    });
                    $scope.editButtonDisabled = false;
                 }
//
        };


                // save package
                var err = 0;
                var errIndx = '';
            $scope.saveInstallmentAction = function (pkg) {

                err= 0; errIndx = '';
                var updatedInstallment = {};

                updatedInstallment.newInst =[];

                pkg.instl.forEach(function (inst, i) {
                    // update the key
                    var key = Number(i) + Number(1);
                    // create an array

                    if(inst.added == true){

                        if( inst.new_duedays = '' || inst.new_date == ''  || inst.new_amnt =='' ){                                      err= 1;
                            errIndx = i;
                            inst.allFields = 1;
                        }
                    }
                        updatedInstallment.newInst[key] = {};
                        updatedInstallment.newInst[key]['instl_id'] = inst.instl_id;
                        updatedInstallment.newInst[key]['new_duedays'] = inst.new_duedays;
                        updatedInstallment.newInst[key]['edited'] = inst.edited;
                        updatedInstallment.newInst[key]['new_amnt'] = inst.new_amnt;
                        updatedInstallment.newInst[key]['new_date'] = inst.new_date;
                        updatedInstallment.newInst[key]['added'] = inst.added;
                        updatedInstallment.newInst[key]['deleted'] = inst.deleted;
                        updatedInstallment.newInst[key]['discounted'] = inst.discounted;

                });


                if( err == 1){
                    //Display errors

                }else{
                    $scope.isDisabled = true; let msg = "", type = "", timeout = 10000;

                    updatedInstallment.package_id = pkg.package_id;
                    updatedInstallment.subs_id = pkg.subs_id;
                    updatedInstallment.pay_id = pkg.pay_id;
                    updatedInstallment.user_id = pkg.user_id;

                    $http({
                        url: installUpdateUrl, method: "POST", data: updatedInstallment,
                    }).then(function (response) {
                        
                        $scope.instlAction.edit=false;
                        $scope.instlAction.instl=-1;
                        $scope.instlAction.instlList='';
                        if (response.data.status) { type = 'success'; timeout = 4500; }
                        else { type = 'danger'; timeout = 9000; }

                        $('body').pgNotification({ style: 'bar', position: 'top', timeout: timeout, type: type, message: response.data.message }).show();
                        $scope.filter.apply();
                        

                    });
                }
            };
        //JA-57 ends

        // View
        $scope.app.name = 'JAWS - Track Payments';

        // User Globals
        $scope.user = _JAWS_USER;

        // Helper Functions
        $scope.trustAsHtml = function (value) {
            return $sce.trustAsHtml(value);
        };
        $scope.ordinal = function (num) {
            var s = ["th", "st", "nd", "rd"],
                v = num % 100;
            return (s[(v - 20) % 10] || s[v] || s[0]);
        };
        function getStatusObj(status) {
            var ret = $scope.statusmodels[9];
            $scope.statusmodels.forEach(function (obj, i) {
                if (obj.status == status) ret = obj;
            });
            return ret;
        };
        function getFuncName() {
            var func = arguments.callee.toString();
            func = func.substr('function '.length);
            func = func.substr(0, func.indexOf('('));
            return func;
        }
        $scope.isNaN = isNaN;
        function serialize(obj, prefix) {
            var str = [], p;
            for (p in obj) {
                if (obj.hasOwnProperty(p)) {
                    var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
                    str.push((v !== null && typeof v === "object") ?
                        serialize(v, k) :
                        encodeURIComponent(k) + "=" + encodeURIComponent(v));
                }
            }
            return str.join("&");
        };

        // Models
        $scope.permissions = {
            approver: defaultSettings.approver,
            pm: defaultSettings.pm
        };
        $scope.instlmodels = [
            {
                name: 'Show All',
                instl_total: ''
            },
            {
                name: 'Full Payment',
                instl_total: 1
            },
            {
                name: '2 Installments',
                instl_total: 2
            },
            {
                name: '3 Installments',
                instl_total: 3
            },
            {
                name: '4 Installments',
                instl_total: 4
            },
            {
                name: '5 Installments',
                instl_total: 5
            }
        ];
        $scope.currencymodels = [
            {
                name: 'Show All',
                currency: '',
                sign: '₹'
            },
            {
                name: 'Indian Rupees (₹)',
                currency: 'inr',
                sign: '₹'
            },
            {
                name: 'US Dollars ($)',
                currency: 'usd',
                sign: '$'
            }
        ];
        $scope.teammodels = [
            {
                name: 'Show All',
                id: ''
            }
        ];
        $scope.statusmodels = [
            {
                name: 'Show All',
                //status: '',
                code: -1,
                color: $scope.permissions.approver ? 'white' : 'white'
            },
            {
                name: 'Draft',
                //status: 'draft',
                code: 0,
                color: $scope.permissions.approver ? 'gray' : 'blue'
            },
            {
                name: 'Approval (Sales)',
                //status: 'approvalsm',
                code: 1,
                color: $scope.permissions.approver ? ($scope.permissions.pm ? 'gray' : 'orange') : 'gray'
            },
            {
                name: 'Approval (Payments)',
                //status: 'approvalpm',
                code: 2,
                color: $scope.permissions.approver ? ($scope.permissions.pm ? 'orange' : 'gray') : 'gray'
            },
            {
                name: 'Rejected',
                //status: 'rejected',
                code: 3,
                color: $scope.permissions.approver ? 'gray' : 'red'
            },
            {
                name: 'Sent',
                //status: 'sent',
                code: 4,
                color: $scope.permissions.approver ? 'white' : 'white'
            },
            {
                name: 'Paid',
                //status: 'paid',
                code: 5,
                color: $scope.permissions.approver ? 'green' : 'green'
            },
            {
                name: 'Due',
                //status: 'due',
                code: 6,
                color: $scope.permissions.approver ? 'white' : 'orange'
            },
            {
                name: 'Expired',
                //status: 'expired',
                code: 7,
                color: $scope.permissions.approver ? 'red' : 'red'
            },
            {
                name: 'Disabled',
                //status: 'disabled',
                code: 8,
                color: $scope.permissions.approver ? 'gray' : 'gray'
            }
        ];
        $scope.sourcemodels = [
            {
                name: 'Show All',
                source: ''
            },
            {
                name: 'Agent Only',
                source: 'user'
            },
            {
                name: 'Website Only',
                source: 'system'
            }
        ];
        $scope.rowcountmodels = [
            {
                name: "10 Per Page",
                count: 10
            },
            {
                name: "20 Per Page",
                count: 20
            }
        ];

        $scope.paymodels = [
            {
                mode: "External Link",
                id: "external",
                desc: "Transaction Reference"
            },
            {
                mode: "Cash",
                id: "cash",
                desc: "Cashier"
            },
            {
                mode: "Cheque",
                id: "cheque",
                desc: "Cheque Number"
            },
            {
                mode: "NEFT",
                id: "neft",
                desc: "Reference Number"
            },
            {
                mode: "Payout",
                id: "payout",
                desc: "Reference Number"
            },
        ];

        $scope.paymodel = {
            mode: '',
            desc: '',
            detail: ''
        };
        $scope.paymodel.mode = $scope.paymodels[0];
        $scope.user_state = '';
        $scope.disable_date_update = '';
        $scope.disable_date = '';

        var courseIdToIndex = [];
        courses.forEach(function (course, i) {
            if (!course.sp_status_inr || !course.sp_status_usd || (!(course.sp_price_inr > 0)) || (!(course.sp_price_usd > 0))) courses[i].premium = true;
            else courses[i].premium = false;

            courseIdToIndex[course.course_id] = i;
        });
        $scope.courses = courses;
        $scope.courseIdToIndex = courseIdToIndex;

        var teamIdToIndex = [];
        defaultSettings.teams.forEach(function (team, i) {
            $scope.teammodels.push({
                name: team.name,
                id: team.team_id
            });
            teamIdToIndex[team.team_id] = i + 1;
        });
        $scope.teamIdToIndex = teamIdToIndex;

        // Filter
        try {
            $scope.filter = {
                lead: (defaultSettings.filter_default !== false) ? defaultSettings.filter_default.lead : '',
                agent: (defaultSettings.filter_default !== false) ? defaultSettings.filter_default.agent : ((!$scope.permissions.approver) ? $scope.user.name : ''),

                price: {
                    min: (defaultSettings.filter_default !== false) ? defaultSettings.filter_default.sum_from : '',
                    max: (defaultSettings.filter_default !== false) ? defaultSettings.filter_default.sum_to : ''
                },

                discount: {
                    min: (defaultSettings.filter_default !== false) ? defaultSettings.filter_default.discount_from : '',
                    max: (defaultSettings.filter_default !== false) ? defaultSettings.filter_default.discount_to : ''
                },

                date: {
                    min: (defaultSettings.filter_default !== false) ? defaultSettings.filter_default.date_from : '',
                    max: (defaultSettings.filter_default !== false) ? defaultSettings.filter_default.date_to : ''
                },

                instl: (defaultSettings.filter_default !== false) ? ((defaultSettings.filter_default.instl_total == '') ? $scope.instlmodels[0] : $scope.instlmodels[defaultSettings.filter_default.instl_total]) : $scope.instlmodels[0],

                currency: (defaultSettings.filter_default !== false) ? (defaultSettings.filter_default.currency == '' ? $scope.currencymodels[0] : (defaultSettings.filter_default.currency == 'inr' ? $scope.currencymodels[1] : $scope.currencymodels[2])) : $scope.currencymodels[0],

                team: (defaultSettings.filter_default !== false) ? (defaultSettings.filter_default.team == '' ? ($scope.permissions.approver ? ($scope.permissions.pm ? $scope.teammodels[0] : $scope.teammodels[$scope.teamIdToIndex[$scope.user.user_id]]) : $scope.teammodels[0]) : ($scope.permissions.approver ? ($scope.permissions.pm ? $scope.teammodels[$scope.teamIdToIndex[defaultSettings.filter_default.team]] : $scope.teammodels[$scope.teamIdToIndex[$scope.user.user_id]]) : $scope.teammodels[0])) : ($scope.permissions.approver ? ($scope.permissions.pm ? $scope.teammodels[0] : $scope.teammodels[$scope.teamIdToIndex[$scope.user.user_id]]) : $scope.teammodels[0]),

                source: (defaultSettings.filter_default !== false) ? (defaultSettings.filter_default.source == '' ? ($scope.permissions.pm ? $scope.sourcemodels[0] : $scope.sourcemodels[1]) : (defaultSettings.filter_default.source == 'user' ? $scope.sourcemodels[1] : ($scope.permissions.pm ? $scope.sourcemodels[2] : $scope.sourcemodels[1]))) : $scope.sourcemodels[$scope.permissions.pm ? 0 : 1],

                status: (defaultSettings.filter_default !== false) ? $scope.statusmodels[defaultSettings.filter_default.status + 1] : $scope.statusmodels[0]
            };
        }
        catch (err) {
            console.log('Error loading saved filters - ' + err.message);

            $('body').pgNotification({
                style: 'bar',
                message: 'Your saved filter preferences were reset due to an error!',
                position: 'top',
                timeout: 2500,
                type: 'warning'
            }).show();

            $scope.filter = {
                lead: '',
                agent: ((!$scope.permissions.approver) ? $scope.user.name : ''),
                price: {
                    min: '',
                    max: ''
                },
                discount: {
                    min: '',
                    max: ''
                },
                date: {
                    min: '',
                    max: ''
                },
                instl: $scope.instlmodels[0],
                currency: $scope.currencymodels[0],
                team: $scope.permissions.approver ? ($scope.permissions.pm ? $scope.teammodels[0] : $scope.teammodels[$scope.teamIdToIndex[$scope.user.user_id]]) : $scope.teammodels[0],
                source: $scope.sourcemodels[$scope.permissions.pm ? 0 : 1],
                status: $scope.statusmodels[0]
            };
        }

        function packageParse(pkg, pkgIndex) {
            try {
                var status = $scope.statusmodels[pkg.status + 1];
                var instl = [];
                var crs = [];

                var courseParse = function (cmbstr, cmbi, free) {
                    if (cmbstr.length < 3) return;

                    var cmbsplit = cmbstr.split(',');
                    var index = courseIdToIndex[parseInt(cmbsplit[0])];
                    var code = cmbsplit[1] == '1' ? courses[index].il_code : courses[index].sp_code;
                    var price = parseInt((cmbsplit[1] === '1') ? (pkg.currency == 'usd' ? courses[index].il_price_usd : courses[index].il_price_inr) : (pkg.currency == 'usd' ? courses[index].sp_price_usd : courses[index].sp_price_inr));
                    if (price === "NaN") price = 0;

                    if (!code) {
                        code = 'NA-' + cmbstr;
                    }
                    crs.push({
                        index: index,
                        course_id: parseInt(cmbsplit[0]),
                        price: price,
                        name: courses[index].name,
                        premium: (cmbsplit[1] === '1'),
                        code: code.length > 1 ? code : courses[index].name,
                        free: free
                    });
                };

                var cmbarr = pkg.combo.split(';');
                cmbarr.forEach(function (cmbstr, cmbi) {
                    courseParse(cmbstr, cmbi, false);
                });

                cmbarr = pkg.combo_free.split(';');
                cmbarr.forEach(function (cmbstr, cmbi) {
                    courseParse(cmbstr, cmbi, true);
                });

                var instl_next = parseInt(pkg.instl_next);
                if (instl_next === 'NaN') instl_next = -1;

                var instlSum = 0;
                pkg.instl.forEach(function (inst, i) {

                    instlSum+= Number(inst.sum);
                    instl.push({
                        due_date: inst.due_date,
                        due_days: inst.due_days,
                        sum: inst.sum,
                        instl_fees: inst.instl_fees,
                        //JA-57 changes starts
                        instl_count : i+1,
                        instl_id : (inst.instl_id)? inst.instl_id:'',
                        edited : (inst.pay_date)? 0 :1,
                        deleted :'',discounted:'',
                        added:'',
                        new_amnt : '',
                        new_duedays : '',
                        new_date : '',
                        amntAdjst : 0,
                        subsStart:pkg.start_date,
                        subsEnd:pkg.end_date,
                        endDueDays:Number(pkg.access_duration),
                        startDueDays:Number(pkg.startDue),
                        allFields : 0,
                        //JA-57 changes ends
                        link: {
                            web_id: inst.web_id,
                            status: inst.paylink_status
                        },

                        payer: {
                            user_id: (inst.payinstl_status == 'paid') ? ((inst.assoc_entity_type == 'agent') ? inst.assoc_entity_id : '') : '',
                            name: (inst.payinstl_status == 'paid') ? ((inst.assoc_entity_type == 'agent') ? inst.assoc_entity_name : 'Self') : '',
                            email: (inst.payinstl_status == 'paid') ? ((inst.assoc_entity_type == 'agent') ? inst.assoc_entity_email : '') : '',
                            phone: (inst.payinstl_status == 'paid') ? ((inst.assoc_entity_type == 'agent') ? inst.assoc_entity_phone : '') : '',
                            type: (inst.payinstl_status == 'paid') ? ((inst.assoc_entity_type == 'agent') ? 'agent' : 'self') : ''
                        },

                        mode: inst.pay_mode,
                        comment: inst.pay_comment,
                        pay_date: (inst.payinstl_status == 'paid') ? inst.pay_date : '',

                        gateway: {
                            name: inst.gateway_name,
                            reference: inst.gateway_reference,
                            channel_info: inst.gateway_channel_info
                        },

                        receipt: inst.receipt,

                        status: inst.payinstl_status
                    });
                });

                let bundle = ""; let bundle_type = "";
                if (pkg.bundle_details) {
                    bundle = pkg.bundle_details.name;
                    bundle_type = pkg.bundle_details.bundle_type;
                }

                let bundle_batch_name = '';
                if (pkg.batch_id) {
                    let batch = '';
                    bootcamps.forEach(function (camps, i) {
                        if (camps.bundle_id == pkg.bundle_id) {
                            camps.batches.forEach(function (camp, j) {
                                if (pkg.batch_id == camp.id) {
                                    batch = camp.meta.name;
                                }
                            })
                        }
                    });
                    bundle_batch_name = batch;
                }

                return {
                    sort_0: pkg.name,
                    sort_1: crs.length,
                    sort_2: pkg.sum_total,
                    sort_3: (((pkg.sum_offered == pkg.sum_total) ? pkg.sum_offered : pkg.sum_basic) - pkg.sum_offered) * 100 / ((pkg.sum_offered == pkg.sum_total) ? pkg.sum_offered : pkg.sum_basic),
                    sort_4: pkg.instl_total,
                    sort_5: pkg.create_date.epoch,
                    sort_6: pkg.agent_name,
                    sort_7: pkg.status,
                    //JA-57 changes
                    user_id:pkg.user_id,
                    instlSum:instlSum,
                    subsStart:pkg.start_date,
                    subsEnd:pkg.end_date,
                    endDueDays:Number(pkg.access_duration),
                    startDueDays:Number(pkg.startDue),
                    //JA-57 changes
                    lead: {
                        name: pkg.name,
                        email: pkg.email,
                        phone: pkg.phone,
                        user_id: pkg.user_id
                    },

                    agent: {
                        name: pkg.agent_name,
                        email: pkg.agent_email,
                        phone: pkg.agent_phone,
                        user_id: pkg.agent_user_id
                    },

                    source: (pkg.agent_name == 'system') ? 'system' : 'user',
                    courses: crs,
                    bundle: bundle,
                    bundle_type: bundle_type,
                    bundle_batch_name: bundle_batch_name,

                    pricing: {
                        currency: pkg.currency,
                        basic: (pkg.sum_offered == pkg.sum_total) ? pkg.sum_offered : pkg.sum_basic,
                        offered: pkg.sum_offered,
                        discount: ((pkg.sum_offered == pkg.sum_total) ? pkg.sum_offered : pkg.sum_basic) - pkg.sum_offered,
                        total: pkg.sum_total
                    },

                    instl_total: pkg.instl_total,
                    instl_next: instl_next,
                    instl: instl,

                    package_index: pkgIndex,
                    package_id: pkg.package_id,
                    pay_id: pkg.pay_id,
                    subs_id: pkg.subs_id,
                    app_num: pkg.app_num,

                    expiry: (pkg.instl.length > 0) ? pkg.instl[0].expire_date : 'No Expiry',
                    date: pkg.create_date,
                    status: {
                        color: status.color,
                        name: status.name,
                        code: status.code,
                        pay: pkg.pay_status,
                        package: pkg.package_status,
                        approval: {
                            sm: (pkg.status_approval_sm == 'approved') ? true : false,
                            pm: (pkg.status_approval_pm == 'approved') ? true : false,
                            sm_comment: pkg.approver_comment_sm,
                            pm_comment: pkg.approver_comment_pm,
                            agent_comment: pkg.agent_comment,
                            auth: {
                                sm: pkg.approver_sm,
                                pm: pkg.approver_pm
                            }
                        }
                    }

                };
            }
            catch (err) {
                console.log('Package - ', pkg);
                console.log('Parse error - ' + err.message);
                return false;
            }
        }

        function packageUpdate(pkg, pkgIndex) {
            var pkgParsed = packageParse(pkg, pkgIndex);
            if (pkgParsed === false) {
                console.log("Parse error on updated package !!");
                $('body').pgNotification({
                    style: 'bar',
                    position: 'top',
                    timeout: 0,
                    type: 'warning',
                    message: 'Warning! Please refresh your filter - failed to retrieve updated package!',
                }).show();
            }
            else $scope.packages[pkgIndex] = pkgParsed;
        }

        $scope.$watch('filter.lead', function (value) {
            if (!$scope.permissions.approver && (value.length == 0)) $scope.filter.source = $scope.sourcemodels[1];
        });

        $scope.filter.reset = function () {
            $scope.filter.lead = '';
            $scope.filter.agent = ((!$scope.permissions.approver) ? $scope.user.name : '');
            $scope.filter.price = {
                min: '',
                max: ''
            };
            $scope.filter.discount = {
                min: '',
                max: ''
            };
            $scope.filter.date = {
                min: '',
                max: ''
            };
            $scope.filter.instl = $scope.instlmodels[0];
            $scope.filter.currency = $scope.currencymodels[0];
            $scope.filter.team = $scope.permissions.approver ? ($scope.permissions.pm ? $scope.teammodels[0] : $scope.teammodels[$scope.teamIdToIndex[$scope.user.user_id]]) : $scope.teammodels[0];
            $scope.filter.source = $scope.sourcemodels[$scope.permissions.pm ? 0 : 1];
            $scope.filter.status = $scope.statusmodels[0];
            $scope.filter.apply();
        };

        $scope.filter.export = function () {

            $window.open(_JAWS_PATH_API + 'package.export?' + serialize({
                lead: $scope.filter.lead,
                agent: ($scope.filter.source.source == 'system') ? '' : ($scope.permissions.approver ? $scope.filter.agent : $scope.user.user_id),
                team: ($scope.permissions.approver ? $scope.filter.team.id : ''),
                currency: $scope.filter.currency.currency,
                sum_from: $scope.filter.price.min,
                sum_to: $scope.filter.price.max,
                discount_from: $scope.filter.discount.min,
                discount_to: $scope.filter.discount.max,
                date_from: $scope.filter.date.min,
                date_to: $scope.filter.date.max,
                instl_total: $scope.filter.instl.instl_total,
                source: $scope.filter.source.source,
                status: $scope.filter.status.code
            }), '_blank');

            $('body').pgNotification({
                style: 'bar',
                message: 'Your download should start automatically..',
                position: 'top',
                timeout: 2500,
                type: 'complete'
            }).show();

        };

        $scope.filter.apply = function (alt = false) {
            $scope.filter.wait = true;

            $http({
                method: 'POST',
                url: _JAWS_PATH_API + 'package.query' + (alt ? '.test' : ''),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                transformRequest: function (obj) {
                    var str = [];
                    for (var p in obj)
                        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                    return str.join("&");
                },

                data: {
                    lead: $scope.filter.lead,
                    agent: ($scope.filter.source.source == 'system') ? '' : ($scope.permissions.approver ? $scope.filter.agent : $scope.user.user_id),
                    team: ($scope.permissions.approver ? $scope.filter.team.id : ''),
                    currency: $scope.filter.currency.currency,
                    sum_from: $scope.filter.price.min,
                    sum_to: $scope.filter.price.max,
                    discount_from: $scope.filter.discount.min,
                    discount_to: $scope.filter.discount.max,
                    date_from: $scope.filter.date.min,
                    date_to: $scope.filter.date.max,
                    instl_total: $scope.filter.instl.instl_total,
                    source: $scope.filter.source.source,
                    status: $scope.filter.status.code
                }

            }).then(function successCallback(response) {

                try {
                    var packages = [], index = 0;

                    response.data.forEach(function (pkg, i) {
                        var pkgParsed = packageParse(pkg, index);
                        if (pkgParsed !== false) {
                            packages.push(pkgParsed);
                            index++;
                        }
                    });

                    $scope.packages = packages;
                    $scope.table.expand = -1;
                    $scope.table.pages.paginate();
                    $scope.filter.wait = false;

                    //JA-57 starts
                    $scope.instlAction.edit = false;
                    $scope.instlAction.instl = -1;
                    //JA-57 ends

                } catch (err) {
                    console.log('Parse error - ' + err.message);
                    $('body').pgNotification({
                        style: 'bar',
                        message: 'Error trying to read data - please try using a different filter. (' + err.message + ')',
                        position: 'top',
                        timeout: 10000,
                        type: 'danger'
                    }).show();
                    $scope.filter.wait = false;
                }

            }, function errorCallback(response) {
                console.log('API Error - ' + response.status);
                $('body').pgNotification({
                    style: 'bar',
                    message: 'Error connecting to JAWS (code ' + response.status + '). You may have been logged out due to inactivity - please refresh this page or try a different filter.',
                    position: 'top',
                    timeout: 0,
                    type: 'danger'
                }).show();
                $scope.filter.wait = false;
            });
        };

        // Table
        $scope.table = {
            pages: {
                rows: {
                    count: $scope.rowcountmodels[0],
                    start: 0
                },
                page: [],
                paginate: function () {
                    $scope.table.wait = true;
                    $scope.table.pages.rows.start = 0;
                    var page_new = [];
                    for (var i = 0; i < $scope.packages.length; i++) if (i % $scope.table.pages.rows.count.count == 0) page_new.push(i);
                    $scope.table.pages.page = page_new;
                    $scope.table.wait = false;
                },
                next: function () {
                    if (($scope.table.pages.rows.start + $scope.table.pages.rows.count.count) < $scope.packages.length) $scope.table.pages.rows.start += $scope.table.pages.rows.count.count;
                },
                prev: function () {
                    if ($scope.table.pages.rows.start > 0) $scope.table.pages.rows.start -= $scope.table.pages.rows.count.count;
                }
            },
            sort: {
                flip: false,
                key: "sort_5",
                apply: function (column) {
                    var key_new = 'sort_' + column.toString();
                    if (key_new == $scope.table.sort.key) $scope.table.sort.flip = !$scope.table.sort.flip;
                    else {
                        $scope.table.sort.key = key_new;
                        $scope.table.sort.flip = false;
                    }
                }
            },
            expand: -1
        };

		/*$scope.user_notify = {
			instl: {
				key: 0,
				value: 'Select Instalment'
			}
		};

		$scope.setInstl = function () {
			$scope.user_notify.instl.value = (($scope.user_notify.instl.key * 1) + 1) + $scope.ordinal((($scope.user_notify.instl.key * 1) + 1)) + ' Instalment';
		};*/

        $scope.allowedToEnable = false;

        // Action
        $scope.action = {
            pkg: -1,
            instl: -1,

            // Wait / In-progress
            wait: false,
            modal: null,
            busy: function (modal) {
                $scope.filter.wait = true;
                $scope.action.wait = true;
                $scope.action.modal = $('#modal-' + modal);
            },
            free: function (msg, type = "info") {
                $scope.filter.wait = false;
                $scope.action.wait = false;
                $scope.action.modal.modal('hide');
                $('body').pgNotification({
                    style: 'bar',
                    position: 'top',
                    timeout: (type == 'danger') ? 10000 : 2500,
                    type: type,
                    message: msg,
                }).show();
            },

            // Enable Package/Installment Modal & Action
            preEnable: function (pkg, instl) {
                let prevInstl = instl - 1;
                $scope.action.pkg = pkg;
                $scope.allowedToEnable = false;
                $scope.action.instl = instl;
                if ((prevInstl === -1) || (prevInstl > -1 && $scope.packages[$scope.action.pkg].instl[prevInstl].status == 'paid')) {
                    $scope.allowedToEnable = true;
                }
            },
            enable: function () {
                $scope.action.busy("notify");
                let prevInstl = $scope.action.instl - 1;
                if ((prevInstl === -1) || (prevInstl > -1 && $scope.packages[$scope.action.pkg].instl[prevInstl].status == 'paid')) {
                    let sendData = {
                        'context': 'enable_payment_link',
                        'email': $scope.packages[$scope.action.pkg].lead.email,
                        'sub_id': $scope.packages[$scope.action.pkg].subs_id,
                        'pay_id': $scope.packages[$scope.action.pkg].pay_id,
                        'instl': Number($scope.action.instl) + Number(1),
                        'disable_date': $scope.disable_date,
                    };
                    $http({
                        url: _JAWS_PATH + "webapi/backend/dash/notify.user", method: "POST", data: sendData
                    }).then(function (response) {
                        $timeout(function () {
                            $scope.action.free(response.data.message, response.data.status ? "success" : "danger");
                        }, 100);
                    });
                }
            },

            // Disable Package/Installment Modal & Action
            preDisable: function (pkg, instl) {
                $scope.action.pkg = pkg;
                $scope.action.instl = instl;
                console.log($scope.packages[$scope.action.pkg].lead);
                let sendData = { 'email': $scope.packages[$scope.action.pkg].lead.email };
                $http({
                    url: _JAWS_PATH + "webapi/backend/dash/user.state", method: "POST", data: sendData
                }).then(function (response) {
                    $scope.user_state = response.data.state;
                });

            },
            editInstl:function(instl,pkg){
                $scope.action.pkg = pkg;
                $scope.action.instl = instl;
                $scope.action.busy("notify");
                let sendData = {
                    'context': 'edit_package',
                    'email': $scope.packages[$scope.action.pkg].lead.email,
                    'sub_id': $scope.packages[$scope.action.pkg].subs_id,
                    'pay_id': $scope.packages[$scope.action.pkg].pay_id,
                    'instl': Number($scope.action.instl) + Number(1),
                    'edit_type': 'edit',
                    'new_amount':1000,
                    'new_date': "2020-03-30"
                };
                $http({
                    url: _JAWS_PATH + "webapi/backend/dash/edit-installment", method: "POST", data: sendData
                }).then(function (response) {
                    $(".modal").modal('hide');
                    $timeout(function () {
                        $scope.action.free(response.data.message, response.data.status ? "success" : "danger");
                    }, 100);
                });

            },
            disable: function () {
                if (!$scope.user_state) {
                    $("#error_msg").html('Please provide user\'s state. In case of USD, please select Other');
                }
                $scope.action.busy("notify");
                let sendData = {
                    'context': 'disable_package',
                    'email': $scope.packages[$scope.action.pkg].lead.email,
                    'sub_id': $scope.packages[$scope.action.pkg].subs_id,
                    'pay_id': $scope.packages[$scope.action.pkg].pay_id,
                    'instl': Number($scope.action.instl) + Number(1),
                    'disable_type': $scope.action.disableMode,
                    'comment': $scope.paymodel.detail,
                    'disable_mode': ($scope.action.disableMode == 'paid') ? $scope.paymodel.mode.id : '',
                    'attach_receipt': true,
                    'state': $scope.user_state,
                    'disable_date_update': $scope.disable_date_update,
                };
                $http({
                    url: _JAWS_PATH + "webapi/backend/dash/notify.user", method: "POST", data: sendData
                }).then(function (response) {
                    $(".modal").modal('hide');
                    $timeout(function () {
                        $scope.action.free(response.data.message, response.data.status ? "success" : "danger");
                    }, 100);
                });
            },

            // Delete Package Modal & Action
            preDelete: function (pkg) {
                $scope.action.pkg = pkg;
            },
            delete: function () {

            },

            // Edit Package Action
            edit: function (pkg) {
                $state.go('app.payment.edit', { p: $scope.packages[pkg].package_id });
            },

            // Notify Installment Modal & Action
            preNotify: function (pkg, instl) {
                $scope.action.pkg = pkg;
                $scope.action.instl = instl;
            },
            notify: function () {
                $scope.action.busy("notify");
                let sendData = {
                    'context': 'notification_email',
                    'email': $scope.packages[$scope.action.pkg].lead.email,
                    'sub_id': $scope.packages[$scope.action.pkg].subs_id,
                    'pay_id': $scope.packages[$scope.action.pkg].pay_id,
                    'instl': Number($scope.action.instl) + Number(1),
                };
                $http({
                    url: _JAWS_PATH + "webapi/backend/dash/notify.user", method: "POST", data: sendData
                }).then(function (response) {
                    $timeout(function () {
                        $scope.action.free(response.data.message, response.data.status ? "success" : "danger");
                    }, 100);
                });
            },

            // Send Invoice Modal & Action
            preInvoice: function (pkg, instl) {
                $scope.action.pkg = pkg;
                $scope.action.instl = instl;
            },
            invoice: function () {
                // not allowed to generate receipt from here.
                /* let sendData = {
                    'context': 'send_receipt',
                    'email': $scope.packages[$scope.action.pkg].lead.email,
                    'name': $scope.packages[$scope.action.pkg].lead.name,
                    'sub_id': $scope.packages[$scope.action.pkg].subs_id,
                    'pay_id': $scope.packages[$scope.action.pkg].pay_id,
                    'instl': Number($scope.action.instl) + Number(1),
                    'state': $scope.user_state
                }; */
                // console.log("ready to use for manual work.");
                // alert("not working");

                /* $http({
                    url: _JAWS_PATH + "webapi/backend/dash/notify.user", method: "POST", data: sendData
                }).then(function (response) {
                    $timeout(function () {
                        $scope.action.free(response.data.message, response.data.status ? "success" : "danger");
                    }, 100);
                }); */
            },

            // Approve Package Modal & Action
            preApprove: function (pkg) {
                $scope.action.pkg = pkg;
                $scope.action.forceApprove = false;
            },
            approve: function () {
                $scope.action.busy("approve");

                let save = {
                    'package': {
                        'package_id': $scope.packages[$scope.action.pkg].package_id,
                        'force': ($scope.permissions.approver && $scope.permissions.pm) ? ($scope.action.forceApprove ? '1' : '0') : '0'
                    }
                };
                if ($scope.permissions.pm) save.package.status_approval_pm = "approved";
                else save.package.status_approval_sm = "approved";

                $http({
                    url: _JAWS_PATH + "webapi/backend/dashtemp/package.update",
                    method: "POST",
                    data: save
                }).then(function (response) {
                    if (response.data.status) packageUpdate(response.data.package, $scope.action.pkg);
                    $timeout(function () {
                        $scope.action.free(response.data.msg + (response.data.status ? '' : "&emsp;[ " + response.data.code + " ]"), response.data.status ? "success" : "danger");
                    }, 100);
                });
            },

            // Reject Package Modal & Action
            preReject: function (pkg) {
                $scope.action.pkg = pkg;
                $scope.action.rejectMsg = '';
            },
            reject: function () {
                $scope.action.busy("reject");
                let save = {
                    'package': {
                        'package_id': $scope.packages[$scope.action.pkg].package_id
                    }
                };

                if ($scope.permissions.pm) {
                    save.package.status_approval_pm = "rejected";
                    save.package.approver_comment_pm = $scope.action.rejectMsg;
                }
                else {
                    save.package.status_approval_sm = "rejected";
                    save.package.approver_comment_sm = $scope.action.rejectMsg;
                }

                $http({
                    url: _JAWS_PATH + "webapi/backend/dashtemp/package.update",
                    method: "POST",
                    data: save,
                }).then(function (response) {
                    if (response.data.status) packageUpdate(response.data.package, $scope.action.pkg);
                    $timeout(function () {
                        $scope.action.free(response.data.msg + (response.data.status ? '' : "&emsp;[ " + response.data.code + " ]"), response.data.status ? "info" : "danger");
                    }, 100);
                });
            }

        };

        // Init
        $scope.packages = [];
        $scope.filter.apply();

        /* Start JA-57 */
        $scope.setSliderDate = function(days,date,index,pkg){ 
           if(date!=undefined){
                var formatedDate = new Date(date);
           }else{ 
                var formatedDate = new Date();
           }
           formatedDate.setDate(formatedDate.getDate() + parseInt(days));
           var updatedDate = $filter('date')(formatedDate, "MM-dd-yyyy");            
           pkg.instl.forEach(function (inst, i) {
                pkg.instl[index].new_date = updatedDate;
            });
            $scope.editButtonDisabled = false;
        }
     /* End JA-57 */

}]);
