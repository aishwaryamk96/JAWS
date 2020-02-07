'use strict';

/* Controllers */

angular.module('jaws')
    .controller('CtrlPaymentEditt', ['$scope', '$state', 'courses', 'specializations', 'defaultSettings', 'apiSVC', '$sce', '$timeout', '$http', '$window', 'packageDetails', 'bootcampDetails', 'programDetails', 'fullstackDetails', function ($scope, $state, courses, specializations, defaultSettings, apiSVC, $sce, $timeout, $http, $window, packageDetails, bootcampDetails, programDetails, fullstackDetails) {

        $scope.trustAsHtml = function (value) {
            return $sce.trustAsHtml(value);
        };

        $scope.ordinal = function (num) {
            var s = ["th", "st", "nd", "rd"],
                v = num % 100;
            return (s[(v - 20) % 10] || s[v] || s[0]);
        }

        $scope.app.name = 'JAWS - New Payment.';

        var courseIdToIndex = [];
        courses.forEach(function (course, i) {
            if (!course.sp_status_inr || !course.sp_status_usd || (!(course.sp_price_inr > 0)) || (!(course.sp_price_usd > 0))) courses[i].premium = false; //updated on 19-2-18
            else courses[i].premium = false;

            // for iot free courses
            if (courses[i].course_id > 47 && courses[i].course_id < 55) courses[i].premium = false;

            courses[i].free = false;
            courses[i].selected = false;
            courseIdToIndex[course.course_id] = i;
        });

        specializations.forEach(function (spec, i) {
            var cmbarr = spec.combo.split(';');
            var crs = [];

            cmbarr.forEach(function (cmbstr, cmbi) {
                var cmbsplit = cmbstr.split(',');
                if (courses[courseIdToIndex[parseInt(cmbsplit[0])]]) {
                    crs.push({
                        course_id: parseInt(cmbsplit[0]),
                        name: courses[courseIdToIndex[parseInt(cmbsplit[0])]].name,
                        premium: (cmbsplit[1] === '1'),
                        enroll: true
                    });
                }
            });

            specializations[i].courses = crs;
        });

        $scope.courses = courses;
        // console.log(courses);

        $scope.specializations = specializations;
        $scope.courseIdToIndex = courseIdToIndex;
        $scope.spec_selected = [];
        $scope.upPriceTimeout = [];

        $scope.specSelect = function (index) {

            // check whether selected specialization has price or not.
            if ($scope.currencyUSD) {
                if (index > -1 && $scope.specializations[index].price_usd == null) {
                    $scope.specializations[index].selected = false;
                    alert('Please select different Specialization as selected specialization does not have USD pricing.');
                    return false;
                }
            } else {
                if (index > -1 && $scope.specializations[index].price_inr == null) {
                    $scope.specializations[index].selected = false;
                    alert('Please select different Specialization as selected specialization does not have INR pricing.');
                    return false;
                }
            }

            $scope.populateElective(index);

            if (index > -1) $scope.spec_selected = [$scope.specializations[index]];
            else $scope.spec_selected = [];

            $scope.specializations.forEach(function (spec, i) {
                if (i != index) $scope.specializations[i].selected = false;
            });

            $scope.courses.forEach(function (course, i) {
                $scope.courses[i].selectedAsSpec = false;
            });

            if (index > -1) {
                $scope.specializations[index].courses.forEach(function (course, i) {
                    $scope.courses[courseIdToIndex[course.course_id]].selectedAsSpec = true;
                    $scope.courses[courseIdToIndex[course.course_id]].selected = false;
                });
            }

            $scope.getWorth();
        };


        $scope.instls = [{
            due: 0,
            sum: 0,
            sumUSD: 0
        }];
        var watchers = [];

        $scope.worth = 0;
        $scope.worthAlt = 0;
        $scope.worthUSD = 0;
        $scope.worthUSDAlt = 0;
        $scope.instl_fees = 0;
        $scope.instl_fees_USD = 0;
        $scope.max_due_date = (defaultSettings.max_due_date) ? defaultSettings.max_due_date : 45;

        $scope.paid = false;
        $scope.discount = 0;
        $scope.notes = {
            complimentary: '',
            discount: {
                reason: '',
                amount: 0
            },
            transaction: {
                mode: '',
                detail: ''
            }
        };

        $scope.mE = false;
        $scope.eI = false;
        $scope.allowPaid = defaultSettings.allow_paymode;
        $scope.eI1 = [];
        $scope.editSum = [];
        $scope.isDisabled = false;
        $scope.elect_course = [];
        $scope.manual_price = 0;

        $scope.transactionModes = [
            { mode: "External Link", id: "external", desc: "Transaction Reference" },
            { mode: "Cash", id: "cash", desc: "Cashier" },
            { mode: "Cheque", id: "cheque", desc: "Cheque Number" },
            { mode: "NEFT", id: "neft", desc: "Reference Number" },
            { mode: "Payout", id: "payout", desc: "Reference Number" },
        ];

        $scope.notes.transaction.mode = $scope.transactionModes[0];

        // bootcamp added
        $scope.newBootcamp = [];
        bootcampDetails.forEach(function (camps, i) {
            if (camps.batches) {
                camps.batches.forEach(function (camp, j) {
                    if (!camp.no_show) {
                        camp.bundle_id = camps.bundle_id;
                        camp.bundle_name = camps.name;
                        camp.combo = camps.combo;
                        camp.selected = false;
                        $scope.newBootcamp.push(camp);
                    }
                })
            }
        });

        $scope.bootcamp_selected = {
            'name': ''
        };
        $scope.campSelect = function (index, bundle_id, batch_id) {
            $scope.bootcamp_selected = {
                'name': ''
            };
            $scope.newBootcamp.forEach(function (batch, i) {
                $scope.newBootcamp[i].selected = false;
                if (batch.bundle_id == bundle_id && batch.id == batch_id && index != -1) {
                    $scope.newBootcamp[i].selected = true;
                    $scope.bootcamp_selected.bundle_id = bundle_id;
                    $scope.bootcamp_selected.combo = batch.combo;
                    $scope.bootcamp_selected.name = batch.bundle_name;
                    $scope.bootcamp_selected.batch_name = batch.meta.name;
                    $scope.bootcamp_selected.batch = index;
                    $scope.bootcamp_selected.batch_id = batch_id;
                    $scope.bootcamp_selected.price = batch.price;
                    $scope.bootcamp_selected.price_usd = batch.price_usd;
                }
            })
            $scope.getWorth();
        };

        // programs added
        $scope.programs = [];
        programDetails.forEach(function (p, i) {
            p.selected = false;
            // COMMENT THE FOLLOWING PART IF THINGS GO SOUTH -------
            if (p.batches) {
                p.batches.forEach(function (batch, j) {
                    if (!batch.no_show) {
                        batch.batch_id = batch.id;
                        batch.bundle_id = p.bundle_id;
                        batch.name = p.name;
                        batch.combo = p.combo;
                        batch.selected = false;
                        batch.price_inr = batch.price;
                        batch.price_usd = batch.price_usd;
                        $scope.programs.push(batch);
                    }
                })
            }
            else {
                $scope.programs.push(p);
            }
        });
        // $scope.programs = programDetails;
        // console.log($scope.programs);
        $scope.program_selected = {
            'name': ''
        };
        $scope.programSelect = function (index, bundle_id, batch_id) {
            $scope.program_selected = {
                'name': ''
            };
            $scope.programs.forEach(function (program, i) {
                $scope.programs[i].selected = false;
                if (index != -1 && program.bundle_id == bundle_id && program.batch_id == batch_id) {
                    $scope.programs[i].selected = true;
                    $scope.program_selected.name = program.name;
                    $scope.program_selected.price_inr = program.price_inr;
                    $scope.program_selected.price_usd = program.price_usd;
                    $scope.program_selected.batch_name = program.meta.name;
                    $scope.program_selected.bundle_id = program.bundle_id;
                    $scope.program_selected.combo = program.combo;
                    $scope.program_selected.batch_id = program.batch_id
                }
            })
            $scope.getWorth();
        };

         // programs added
        $scope.fullstacks = [];
        fullstackDetails.forEach(function (p, i) {
            p.selected = false;
            // COMMENT THE FOLLOWING PART IF THINGS GO SOUTH -------
            if (p.batches) {
                p.batches.forEach(function (batch, j) {
                    if (!batch.no_show) {
                        batch.price = p.price_inr;
                        batch.batch_id = batch.id;
                        batch.bundle_id = p.bundle_id;
                        batch.name = p.name;
                        batch.combo = p.combo;
                        batch.selected = false;
                        batch.price_usd = p.price_usd;
                        batch.price_inr = p.price_inr;
                        $scope.fullstacks.push(batch);
                    }
                })
            }
            else {
                $scope.fullstacks.push(p);
            }
        });
        // $scope.fullstacks = fullstackDetails;
        $scope.fullstack_selected = {
            'name': ''
        };
        
        $scope.fullstackSelect = function (index, bundle_id, batch_id) {
            $scope.fullstack_selected = {
                'name': ''
            };
            $scope.fullstackNoOfCourseSelected = [];
            $scope.fullstacks.forEach(function (fullstack, i) {
                $scope.fullstacks[i].selected = false;
                if (index != -1 && fullstack.bundle_id == bundle_id && fullstack.batch_id == batch_id) { 
                    $scope.fullstacks[i].selected = true;
                    $scope.fullstack_selected.name = fullstack.name;
                    $scope.fullstack_selected.price_inr = fullstack.price_inr;
                    $scope.fullstack_selected.price_usd = fullstack.price_usd;
                    $scope.fullstack_selected.batch_name = fullstack.meta.name;
                    $scope.fullstack_selected.bundle_id = fullstack.bundle_id;
                    $scope.fullstack_selected.combo = fullstack.combo;
                    $scope.fullstack_selected.batch_id = fullstack.batch_id;
                    $scope.fullstackNoOfCourseSelected.push($scope.fullstack_selected);  
                }
            })
            $scope.getWorth();
        };

        $scope.$watch('discount', function (value) {
            $scope.upPrice(value);
        });
 
        $scope.populateElective = function (index) {
            /* if (index != -1 && $scope.specializations[index].electives) {
                angular.element(document.querySelector('#initiateElective')).click();
                $scope.elect_course = $scope.specializations[index].electives;
                $scope.elect_course_text = $scope.specializations[index].electives_str;
            } else {
                $scope.elect_course = [];
            } */
        }

        $scope.newEdit = function (value, id) {

            var sum = Math.ceil((($scope.worth * ((100 - $scope.discount) / 100)) + $scope.instl_fees) * 1.18),
                sumUSD = Math.ceil(($scope.worthUSD * ((100 - $scope.discount) / 100)) + $scope.instl_fees_USD),
                oneSum = Math.ceil(Number(sum / $scope.instls.length)),
                oneSumUSD = Math.ceil(Number(sumUSD / $scope.instls.length));

            var eSum = $scope.editSum.reduce((a, b) => Number(a) + Number(b), 0),
                rInstl = ($scope.instls.length - 1) - id,
                diffSum = (rInstl > 0) ? (($scope.currencyUSD) ? sumUSD - eSum : sum - eSum) / rInstl : (($scope.currencyUSD) ? sumUSD - eSum : sum - eSum) / 1;

            var someFunc2ConvertUserInputINR2USDvalue = 0,
                someFunc2ConvertUserInputUSD2INRvalue = 0;

            $scope.instls.forEach(function (instl, i) {
                oneSum = $scope.editSum[i];
                oneSumUSD = $scope.editSum[i];
                if (i > id) {

                    $scope.instls[i].sum = Math.ceil((($scope.currencyUSD) ? Number(oneSum) : Number(oneSum) + Number(diffSum)));

                    $scope.instls[i].sumUSD = Math.ceil(($scope.currencyUSD) ? Number(oneSumUSD) + Number(diffSum) : Number(oneSumUSD));

                    $scope.editSum[i] = ($scope.currencyUSD) ? Math.ceil((Number(oneSumUSD) + Number(diffSum))) : Math.ceil(Number(oneSum) + Number(diffSum));
                } else if (i === id) {

                    $scope.instls[i].sum = (($scope.currencyUSD) ? Number(oneSum + someFunc2ConvertUserInputUSD2INRvalue) : Math.ceil(Number(value)));

                    $scope.instls[i].sumUSD = Math.ceil(($scope.currencyUSD) ? Number(value) : Number(oneSumUSD + someFunc2ConvertUserInputINR2USDvalue));
                }
            });
            $scope.eI1[id] = !$scope.eI1[id];
            // remove the input box on  blur
            $scope.eI = !$scope.eI;
        };

        $scope.upPrice = function (value) {
            var prc = ($scope.currencyUSD) ? $scope.worthUSD : $scope.worth;

            try {
                $timeout.cancel($scope.upPriceTimeout);
            }
            catch (err) { }

            if (typeof value === "undefined") {
                $scope.upPriceTimeout = $timeout(function () {
                    value = $scope.notes.discount.amount;

                    if (defaultSettings.superuser) {
                        if (value > prc) {
                            alert("Cannot give more than 100% discount..");
                        } else {
                            $scope.discount = ((value / prc) * 100).toFixed(3);
                        }
                    } else {
                        if (value >= (prc - 1)) {
                            alert("Cannot give more than 99% discount.");
                        } else {
                            $scope.discount = ((value / prc) * 100).toFixed(3);
                        }
                    }
                }, 1500);
            }

            else if (value == -1) {
                value = $scope.notes.discount.amount;
                if (defaultSettings.superuser) {
                    if (value > prc) {
                        alert("Cannot give more than 100% discount.");
                    } else {
                        $scope.discount = ((value / prc) * 100).toFixed(3);
                    }
                } else {
                    if (value >= (prc - 1)) {
                        alert("Cannot give more than 99% discount.");
                    } else {
                        $scope.discount = ((value / prc) * 100).toFixed(3);
                    }
                }
            }

            else $scope.notes.discount.amount = (($scope.discount > 0) ? (prc * ($scope.discount / 100)) : 0).toFixed();
        };

        $scope.getWorth = function (manual) {

            var worth = 0, worthUSD = 0, worthAlt = 0, worthUSDAlt = 0, selectedEntity = ""; let isSpecSelected = false;

            $scope.courses.forEach(function (course, i) {
                if ((course.selected) && (!course.free)) {
                    worth += parseInt(course.premium ? course.il_price_inr : course.sp_price_inr);
                    worthUSD += parseInt(course.premium ? course.il_price_usd : course.sp_price_usd);
                    worthAlt += parseInt(course.premium ? course.il_price_inr_alt : course.sp_price_inr_alt);
                    worthUSDAlt += parseInt(course.premium ? course.il_price_usd_alt : course.sp_price_usd_alt);
                }
            });

            if ($scope.fullstack_selected.name.length) {
                if (selectedEntity == "") {
                    worth += parseInt($scope.fullstack_selected.price_inr);
                    worthUSD += parseInt($scope.fullstack_selected.price_usd);
                    selectedEntity = "Full Stack";
                } else {
                    $scope.fullstack_selected.forEach(function (camps, i) { $scope.fullstack_selected[i].selected = false; });
                    $scope.fullstack_selected = {
                        'name': ''
                    };
                    alert('Full stack cannot be selected with ' + selectedEntity);
                }
            }

            else if ($scope.spec_selected.length > 0) {
                if (selectedEntity == "") {
                    worth += parseInt($scope.spec_selected[0].price_inr);
                    worthUSD += parseInt($scope.spec_selected[0].price_usd);
                    selectedEntity = "Specialization";
                    isSpecSelected = true;
                }
                else {
                    $scope.spec_selected.forEach(function (camps, i) { $scope.spec_selected[i].selected = false; });
                    $scope.spec_selected = {
                        'name': ''
                    };
                    alert('Specialization cannot be selected with ' + selectedEntity);
                }
            }

            else if ($scope.bootcamp_selected.name.length) {
                if (selectedEntity == "") {
                    worth += parseInt($scope.bootcamp_selected.price);
                    worthUSD += parseInt($scope.bootcamp_selected.price_usd);
                    selectedEntity = "Bootcamp";
                } else {
                    $scope.newBootcamp.forEach(function (camps, i) { $scope.newBootcamp[i].selected = false; });
                    $scope.bootcamp_selected = {
                        'name': ''
                    };
                    alert('Bootcamp cannot be selected with ' + selectedEntity);
                }
            }

            else if ($scope.program_selected.name.length) {
                if (selectedEntity == "") {
                    worth += parseInt($scope.program_selected.price_inr);
                    worthUSD += parseInt($scope.program_selected.price_usd);
                    selectedEntity = "Program";
                } else {
                    $scope.program_selected.forEach(function (camps, i) { $scope.program_selected[i].selected = false; });
                    $scope.program_selected = {
                        'name': ''
                    };
                    alert('Program cannot be selected with ' + selectedEntity);
                }
            }

            else {
                selectedEntity = "";
            }

            $scope.worth = worth;
            $scope.worthUSD = worthUSD;
            $scope.worthAlt = worthAlt;
            $scope.worthUSDAlt = worthUSDAlt;

            if (manual != undefined) {
                $scope.worth = $scope.manual_price;
                $scope.worthUSD = $scope.manual_price;
            }

            $scope.reInstl();
        };

        $scope.getInstlFees = function () {
            var fees = 0, feesUSD = 0, flag = false;;
            console.log("a"+defaultSettings.instalment_fees.inr);
            console.log("installment Total" + $scope.instls.length);
            $scope.instls.forEach(function (instl, i) {
                /*if (instl.due > 15) {*/
                if (instl.due > 7) {
                    //fees += defaultSettings.instalment_fees.inr;
                    //feesUSD += defaultSettings.instalment_fees.usd;
                    flag = true;
                }
            });

            if (flag) {
                fees += defaultSettings.instalment_fees.inr;
                feesUSD += defaultSettings.instalment_fees.usd;
            }

            $scope.instl_fees = fees;
            $scope.instl_fees_USD = feesUSD;
            $scope.reInstl();
        };

        $scope.reInstl = function () {
            var sum = ((($scope.worth * ((100 - $scope.discount) / 100)) + $scope.instl_fees) * 1.18) / $scope.instls.length,
                sumUSD = (($scope.worthUSD * ((100 - $scope.discount) / 100)) + $scope.instl_fees_USD) / $scope.instls.length;
            $scope.courses.forEach(function (course, i) {
                if ((course.selected) && (!course.free)) {
                    if (course.course_id == 150) {
                        sum = Math.floor((($scope.worth * ((100 - $scope.discount) / 100)) + $scope.instl_fees) * 1.18) / $scope.instls.length
                    } else if (course.course_id == 219) {
                        sum = Math.ceil((($scope.worth * ((100 - $scope.discount) / 100)) + $scope.instl_fees) * 1.18) / $scope.instls.length
                    }
                }
            });

            $scope.instls.forEach(function (instl, i) {
                $scope.instls[i].sum = sum;
                $scope.instls[i].sumUSD = sumUSD;
                $scope.editSum[i] = ($scope.currencyUSD) ? sumUSD.toFixed() : sum.toFixed();
            });
        };


        $scope.getInstallmentDate = function(index){
            
            var today = new Date();
            var date1 = new Date($scope.instls[index].due_date);
            if(date1 >= today){
                var date2 = new Date(today);
                var timeDiff = Math.abs(date2.getTime() - date1.getTime());
                var dayDifference = Math.ceil(timeDiff / (1000 * 3600 * 24));
                $scope.instls[index].due = dayDifference; 
            }else{
                $scope.instls[index].due = defaultSettings.instalment_date; 
            }
                
      }


        $scope.instlFees = function() {
            // $scope.instl_fees = $scope.instl_fees_amt;
            defaultSettings.instalment_fees.inr = $scope.instl_fees_amt;
            defaultSettings.instalment_fees.usd = $scope.instl_fees_amt;
            $scope.getInstlFees();
        }

        $scope.addInstl = function () {
            $scope.instls.push({
                due: defaultSettings.instalment_date,
                sum: 0,
                sumUSD: 0
            });

            watchers.push($scope.$watch('instls[' + ($scope.instls.length - 1) + '].due', function (newVal, oldVal, scope) {
                $scope.getInstlFees();
            }));

            $scope.getInstlFees();
        };

        $scope.remInstl = function (index) {
            watchers.forEach(function (watcher, i) {
                watchers[i]();
            });

            $scope.instls.splice(index, 1);
            watchers = [];

            $scope.instls.forEach(function (instl, i) {
                if (i > 0) watchers.push($scope.$watch('instls[' + i + '].due', function (newVal, oldVal, scope) {
                    $scope.getInstlFees();
                }));
            });

            $scope.getInstlFees();
        };

        $scope.$watch('discount', function (newVal, oldVal, scope) {
            $scope.reInstl();
        });

        $scope.saveMode = "";
        $scope.selectMode = function (value, id) {
            console.log(value, 'selectMode');
            $scope.saveMode = value;
        };

        $scope.user = {
            email: '',
            name: '',
            phone: ''
        };

        $scope.user_state = '';

        $scope.subsmodels = [
            {
                name: 'Course Based Duration',
                duration: 0
            },
            {
                name: '3 Months Subscription',
                duration: 3
            },
            {
                name: '6 Months Subscription',
                duration: 6
            },
            {
                name: '12 Months Subscription',
                duration: 12
            },
            {
                name: 'Lifetime Access',
                duration: 60
            }
        ];

        $scope.accesstypes = [
            {
                name: 'Normal Account',
                type: 'soc'
            },
            {
                name: 'Corporate Account',
                type: 'corp'
            }
        ];

        $scope.accesstypes.selected = $scope.accesstypes[0];
        $scope.subsmodels.selected = $scope.subsmodels[0];

        var t_user;

        $scope.$watch('user.email', function (newVal, oldVal, scope) {

            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

            if (re.test(newVal)) {
                try { $timeout.cancel(t_user); } catch (e) { }

                t_user = $timeout(function () {
                    $http.get(_JAWS_PATH_API + 'user.get.kform?email=' + encodeURIComponent(newVal)).then(function (response) {
                        if (response.data.status) {
                            $scope.user.name = response.data.name;
                            $scope.user.phone = response.data.phone;

                            if ((response.data.soc_lms !== undefined) && (response.data.soc_lms !== null)) {
                                if (response.data.soc_lms == 'corp') $scope.accesstypes.selected = $scope.accesstypes[1];
                                else $scope.accesstypes.selected = $scope.accesstypes[0];
                            }

                            return true;
                        }
                        return false;
                    });
                    $http({
                        url: _JAWS_PATH + "webapi/backend/dash/user.state", method: "POST", data: { 'email': newVal }
                    }).then(function (response) {
                        $scope.user_state = response.data.state;
                    });
                }, 350);
            }

        });

        $scope.package_id = "";

        // Load package details
        if (packageDetails) {

            console.log(packageDetails);

            $scope.package_id = packageDetails.package_id;

            let tempCourse = "";
            let tempCourseFree = "";
            let tempPackDetails = (packageDetails.serialized) ? JSON.parse(packageDetails.serialized) : "";
            let tempSpec = (packageDetails.bundle_id) ? packageDetails.bundle_id : "";
            let tempSpecCombo = "";
            let tempSpecUnsel = "";
            let tempInstlArr = JSON.parse(packageDetails.instl);
            let comments = JSON.parse(packageDetails.creator_comment);

            // for instalments
            if (tempInstlArr) {
                $scope.instls = [];
                for (var key in tempInstlArr) {
                    if (tempInstlArr.hasOwnProperty(key) && tempInstlArr[key].sum) {
                        $scope.instls.push({
                            due: tempInstlArr[key].due_days,
                            sum: tempInstlArr[key].sum,
                            sumUSD: 0
                        });
                    }
                }
            }

            // for courses
            if (packageDetails.combo) { tempCourse = packageDetails.combo.split(";"); }
            if (tempCourse) {
                tempCourse.forEach(function (value, key) {
                    let tempCid = value.split(","); /*course id = tempCid[0], course mode = tempCid[1];*/
                    let selc = courseIdToIndex[tempCid[0]];
                    $scope.courses[selc].selected = true;
                    /*premium mode = 1, regular mode = 2*/
                    if (tempCid[1] == 1) $scope.courses[selc].premium = true;
                });
            }

            // for free courses
            if (packageDetails.combo_free) { tempCourseFree = packageDetails.combo_free.split(";"); }
            if (tempCourseFree) {
                tempCourseFree.forEach(function (value, key) {
                    let tempCid = value.split(","); /*course id = tempCid[0], course mode = tempCid[1];*/
                    let selc = courseIdToIndex[tempCid[0]];
                    $scope.courses[selc].selected = true;
                    $scope.courses[selc].free = true;
                    /*premium mode = 1, regular mode = 2*/
                    if (tempCid[1] == 1) $scope.courses[selc].premium = true;
                });
            }

            // for specializations
            if (tempSpec) {
                $scope.specializations.forEach(function (spec, index) {
                    if (tempSpec == spec.bundle_id) {
                        $scope.specializations[index].selected = true;
                        $scope.specSelect(index);
                    }
                });
            }
            if ($scope.spec_selected[0]) {
                if (tempPackDetails.data_bundle_combo) { tempSpecCombo = tempPackDetails.data_bundle_combo.split(";"); }
                if (tempPackDetails.data_bundle_unselect) { tempSpecUnsel = tempPackDetails.data_bundle_unselect.split(";"); }
                $scope.spec_selected[0].courses.forEach(function (course, i) {
                    // handle enroll and premium here.
                    if (tempSpecCombo) {
                        tempSpecCombo.forEach(function (value, key) {
                            let tempSpecComboid = value.split(","); /*course id = tempSpecComboid[0], course mode = tempSpecComboid[1];*/
                            if (tempSpecComboid[0] == course.course_id) {
                                $scope.spec_selected[0].courses[i].enroll = true;
                                courses[courseIdToIndex[tempSpecComboid[0]]].selectedAsSpec = true;
                            }
                            if (tempSpecComboid[1] == 1) $scope.spec_selected[0].courses[i].premium = true;
                        });
                    }
                    if (tempSpecUnsel) {
                        tempSpecUnsel.forEach(function (value, key) {
                            let tempSpecUnselid = value.split(","); /*course id = tempSpecUnselid[0], course mode = tempSpecUnselid[1];*/
                            if (tempSpecUnselid[0] == course.course_id) {
                                $scope.spec_selected[0].courses[i].enroll = false;
                                courses[courseIdToIndex[tempSpecUnselid[0]]].selectedAsSpec = false;
                            }
                            if (tempSpecUnselid[1] == 1) $scope.spec_selected[0].courses[i].premium = true;
                        });
                    }
                });
            }

            // for 1st instalment paid or not
            if (packageDetails.pay_mode == "online") {
                $scope.paid = true;
                $scope.notes.transaction.mode.id = "online";
                $scope.notes.transaction.detail = comments.misc;
            }

            // user details
            $scope.user.email = packageDetails.email;
            $scope.user.name = packageDetails.name;
            $scope.user.phone = packageDetails.phone;

            // currency details
            if (packageDetails.currency == "inr") {
                $scope.currencyUSD = false;
            } else {
                $scope.currencyUSD = true;
            }

            // discount details
            $scope.discount = Number(tempPackDetails.data_payment_discount);
            $scope.notes.discount.reason = comments.discount;

            // enable button action
            // $scope.isDisabled = false;
        }

        if ($scope.user_state.length == 0 || !$scope.user_state) {
            $scope.isDisabled == true;
        }

        var getMonthOrDate = d => {
            if (typeof d == "object") {
                return getMonthOrDate(d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate());
            }
            if (d < 10) {
                return "0" + d;
            }
            return d;
        }

        // save package
        $scope.submitPackage = function (reload) {
            console.log("Submit button clicked");

            if (typeof reload == "undefined") reload = true;

            var instl_arr = [], combo = [], combo_free = [], courses_amount = { inr: 0, usd: 0, combo_inr: 0, combo_usd: 0 }, unselect_bundle = [], spec_combo = [];

            $scope.courses.forEach(function (course, i) {
                if ((course.selected) && (!course.free)) {

                    courses_amount.inr += Number(course.premium ? course.il_price_inr : course.sp_price_inr);

                    courses_amount.usd += Number(course.premium ? course.il_price_usd : course.sp_price_usd);

                    courses_amount.combo_inr += Number(course.premium ? course.il_price_inr_alt : course.sp_price_inr_alt);

                    courses_amount.combo_usd += Number(course.premium ? course.il_price_usd_alt : course.sp_price_usd_alt);

                    if (course.premium) {
                        combo.push(course.course_id + ',1');
                    } else {
                        combo.push(course.course_id + ',2');
                    }
                } else if ((course.selected) && (course.free)) {
                    if (course.premium) {
                        combo_free.push(course.course_id + ',1');
                    } else {
                        combo_free.push(course.course_id + ',2');
                    }
                }
            });

            var offUSD = Math.ceil($scope.worthUSD * ((100 - $scope.discount) / 100));
            var offINR = Math.ceil($scope.worth * ((100 - $scope.discount) / 100));
            var totalUSD = Math.ceil(($scope.worthUSD * ((100 - $scope.discount) / 100)) + $scope.instl_fees_USD);
            var totalINR = Math.ceil((($scope.worth * ((100 - $scope.discount) / 100)) + $scope.instl_fees) * 1.18);

            instl_arr.push(new Array("Start from index 1 pls"));
            $scope.instls.forEach(function (inst, i) {
                // update the key
                var key = Number(i) + Number(1);
                // create an array
                instl_arr[key] = {};
                instl_arr[key]['sum'] = ($scope.currencyUSD) ? Math.ceil(inst.sumUSD) : Math.ceil(inst.sum);
                instl_arr[key]['due_days'] = inst.due;
                if (inst.due_date) {
                    var date = new Date(inst.due_date);
                    instl_arr[key]['due_date'] = getMonthOrDate(date);
                }
            });

            var creator_token = "",
                combo = combo.join(';'),
                combo_free = combo_free.join(';'),
                currency = ($scope.currencyUSD) ? "usd" : "inr",
                sum_basic = ($scope.currencyUSD) ? Math.ceil($scope.worthUSD) : Math.ceil($scope.worth),
                sum_offered = ($scope.currencyUSD) ? offUSD : offINR,
                sum_total = ($scope.currencyUSD) ? totalUSD : totalINR,
                mode = ($scope.paid == false) ? "online" : $scope.notes.transaction.mode.id,
                courses_actual = ($scope.currencyUSD) ? Math.ceil(courses_amount.usd) : Math.ceil(courses_amount.inr),
                courses_combo = ($scope.currencyUSD) ? Math.ceil(courses_amount.combo_usd) : Math.ceil(courses_amount.combo_inr),
                courses_discount = "if combo > 0, then diff between courses_actual and courses_combo as per currency gives courses_discount",
                payment_discount = $scope.discount,
                tax_amount = Math.ceil(($scope.currencyUSD) ? 0 : ((($scope.worth * ((100 - $scope.discount) / 100)) + $scope.instl_fees) * 0.18)),
                discount_amount = $scope.notes.discount.amount,
                offered_amount = ($scope.currencyUSD) ? offUSD : offINR,
                instalment_amount = ($scope.currencyUSD) ? defaultSettings.instalment_fees.usd : defaultSettings.instalment_fees.inr,
                net_payable = ($scope.currencyUSD) ? totalUSD : totalINR,
                edit_offered = "",
                edit_discount = "",
                edit_percent = "",
                edit_tax = "",
                tax = ($scope.currencyUSD) ? defaultSettings.tax_rate.usd : defaultSettings.tax_rate.inr,
                instl_fees = ($scope.currencyUSD) ? $scope.instl_fees_USD : $scope.instl_fees,
                instl_total = $scope.instls.length;

            var save = {

                'package': {
                    'combo': combo,
                    'combo_free': combo_free,
                    'currency': currency,
                    'sum_basic': sum_basic,
                    'sum_offered': sum_offered,
                    'sum_total': sum_total,
                    'tax': tax,
                    'instl': instl_arr,
                    'instl_fees': instl_fees,
                    'instl_total': instl_total,
                    'email': $scope.user.email,
                    'name': $scope.user.name,
                    'phone': $scope.user.phone,
                    'create_date': new Date(),
                    'creator_type': "agent",
                    'pay_mode': mode,
                    'data_courses_actual': courses_actual,
                    'data_courses_combo': courses_combo,
                    'data_courses_discount': courses_discount,
                    'data_payment_discount': payment_discount,
                    'data_tax_amount': tax_amount,
                    'data_discount_amount': discount_amount,
                    'data_offered_amount': offered_amount,
                    'data_instalment_amount': instalment_amount,
                    'data_net_payable': net_payable,
                    'data_edit_offered_price': edit_offered,
                    'data_edit_discount_amount': edit_discount,
                    'data_edit_discount_percent': edit_percent,
                    'data_edit_tax_amount': edit_tax,
                    'data_instalment_fees_inr': defaultSettings.instalment_fees.inr,
                    'data_instalment_fees_usd': defaultSettings.instalment_fees.usd,
                    'data_kform_version': 'kform-angular',
                    'data_user_state': $scope.user_state,
                    'expire_date': $scope.expiry,
                    'course_start_date': $scope.course_start,
                }
            };

            if ($scope.spec_selected.length > 0) {
                save['package']['bundle_id'] = $scope.spec_selected[0].bundle_id;
                save['package']['data_bundle_price'] = ($scope.currencyUSD) ? Math.ceil($scope.spec_selected[0].price_usd) : Math.ceil($scope.spec_selected[0].price_inr);
                save['package']['data_bundle_combo'] = $scope.spec_selected[0].combo;
                $scope.spec_selected[0].courses.forEach(function (course, i) {
                    if (course.enroll && !course.premium) { /*enroll =  true , premium = false*/
                        spec_combo.push(course.course_id + ',2');
                    } else if (course.enroll && course.premium) { /*enroll = true , premium = true*/
                        spec_combo.push(course.course_id + ',1');
                    } else if (!course.enroll && !course.premium) { /*enroll = false , premium = false*/
                        unselect_bundle.push(course.course_id + ",2");
                    } else if (!course.enroll && course.premium) { /*enroll = false, premiium = true*/
                        unselect_bundle.push(course.course_id + ",1");
                    } else { alert("Some error occured in specialization"); return false; }
                });
                save['package']['data_bundle_unselect'] = (unselect_bundle) ? unselect_bundle.join(";") : "";
                if (spec_combo) {
                    if (combo) {
                        save['package']['combo'] = save['package']['combo'] + ";" + spec_combo.join(";");
                    } else {
                        save['package']['combo'] = spec_combo.join(";");
                    }
                }
            }

            save['package']['creator_comment'] = {},
                save['package']['creator_comment']["instl"] = "";
            save['package']['creator_comment']["combo_free"] = $scope.notes.complimentary;
            save['package']['creator_comment']["discount"] = $scope.notes.discount.reason;
            save['package']['creator_comment']["misc"] = $scope.notes.transaction.detail;

            if ($scope.package_id) save['package']['package_id'] = $scope.package_id;

            if ($scope.saveMode == "save") {
                var sendUrl = _JAWS_PATH + "webapi/backend/dashtemp/package.create";
            } else {
                var sendUrl = _JAWS_PATH + "webapi/backend/dashtemp/package.send";
            }

            if ($scope.bootcamp_selected.name.length) {
                // if bootcamp update the data.
                save['package']['bundle_id'] = $scope.bootcamp_selected.bundle_id;
                save['package']['combo'] += ((save['package']['combo']) ? ';' : '') + $scope.bootcamp_selected.combo;
                save['package']['batch_id'] = $scope.bootcamp_selected.batch_id;
            }

            if ($scope.program_selected.name.length) {
                // if program update the data.
                save['package']['bundle_id'] = $scope.program_selected.bundle_id;
                save['package']['combo'] += ((save['package']['combo']) ? ';' : '') + $scope.program_selected.combo;
                save['package']['batch_id'] = $scope.program_selected.batch_id;
            }

            if ($scope.fullstack_selected.name.length) {
                // if program update the data.
                save['package']['bundle_id'] = $scope.fullstack_selected.bundle_id;
                save['package']['combo'] += ((save['package']['combo']) ? ';' : '') + $scope.fullstack_selected.combo;
                save['package']['batch_id'] = $scope.fullstack_selected.batch_id;
            }

            if(save['package']['bundle_id'] == '129'){
                save['package']['course_start_date'] = '15/07/2019';
            }

            console.log(save); console.log(sendUrl); console.log(reload);
            // alert('Please wait. Update in progress. Try after sometime.');
            // return false; // uncomment this line to stop package creation.

            $scope.isDisabled = true; let msg = "", type = "", timeout = 10000;

            $http({
                url: sendUrl, method: "POST", data: save,
            }).then(function (response) {
                console.log(response);
                $(".modal").modal("hide");

                if (response.data.status) { type = 'success'; msg = response.data.msg; timeout = 4500; }
                else { type = 'danger'; msg = response.data.msg + "&emsp;[ " + response.data.code + " ]"; }

                $('body').pgNotification({ style: 'bar', position: 'top', timeout: timeout, type: type, message: msg }).show();

                $scope.package_id = response.data.package_id; $scope.isDisabled = false;

                if (reload) $timeout(function () { $state.reload(); }, 5000);
            });
        };

    }]);
