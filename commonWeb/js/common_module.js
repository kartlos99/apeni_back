/**
 * Created by k.diakonidze on 8/15/19.
 */

var optionChoose = "...აირჩიეთ";
var techPosArray = [0, 0, 0];
var techDataArray = ["0", "0", "0"];
var criteriasOnTechPosArray = [];
var criteriaPosArray = [];
var criteriaDataArray = [];
var categoryObj;
var waitingItem;

var text_chooseModel = "აირჩიეთ მოდელი!";
var text_PriceAndCriteriaWeightStatusAlert = "ღირებულებისა და კრიტერიუმების წონების სტატუსი არააქტიურია!";
var text_NotFound = "ჩანაწერი ვერ მოიძებნა!";
var orgSelector = $('#organization_id');
var filSelector = $('#filial_id');
var solverSelector = $('#SolverID_id');
var waitForDropdowns = 0;

function f_show() {
}

function f_hide() {
}

var pageJS = $('#currUserdata').attr("data-page");
console.log("currPage:", pageJS);
let mainMenu = $('ul.components');
mainMenu.find('li').removeClass('active');
mainMenu.find('li.' + pageJS).addClass('active');

let tkn = "Bearer " + $('#currUserdata').attr("data-tkn");
// window.localStorage.setItem('tkn', tkn);
$('#currUserdata').attr("data-tkn", "-");

function printout(x) {
    console.log("printed:", x);
}

let monthObj = {
    "1": "იანვარი",
    "2": "თებერვალი",
    "3": "მარტი",
    "4": "აპრილი",
    "5": "მაისი",
    "6": "ივნისი",
    "7": "ივლისი",
    "8": "აგვისტო",
    "9": "სექტემბერი",
    "10": "ოქტომბერი",
    "11": "ნოემბერი",
    "12": "დეკემბერი"
}

function showError(code, text) {
    if (code == 401)
        var res = confirm("საჭიროებს ავტორიზაციის გავლას!");
        if (res == true) {
            window.location.replace("logout.php");
        }
    else
        alert(text)
}

function getFormatedDate() {
    let date = new Date();
    let dt = date.getFullYear() + "-";
    let month = date.getMonth() + 1;
    if (month < 10) month = "0" + month;
    dt += month + "-";
    let day = date.getDate();
    if (day < 10) day = "0" + day;
    dt += day;
    return dt;
}

function groupBy(list, keyGetter) {
    const map = new Map();
    list.forEach((item) => {
        const key = keyGetter(item);
        const collection = map.get(key);
        if (!collection) {
            map.set(key, [item]);
        } else {
            collection.push(item);
        }
    });
    return map;
}

function loadTypesList(parentID, selector, pos = 0) {
    var data = {
        'parentID': parentID
    };

    $.ajax({
        url: 'php_code/get_tech_list.php',
        method: 'get',
        data: data,
        dataType: 'json',
        success: function (response) {

            var selEl = $('#' + selector);
            selEl.empty();

            if (response.length > 0) {
                $('<option />').text(optionChoose).val(0).appendTo(selEl);
                response.forEach(function (item) {
                    $('<option />').text(item.Name).val(item.id).appendTo(selEl);
                });

                var nn = selEl.data("nn");
                techPosArray[nn] = selEl.val();
                console.log(techPosArray);

                techDataArray[nn] = response;
                console.log(techDataArray);

                if (pos.length > 0) {
                    selEl.val(pos);
                } else {
                    selEl.trigger('change');
                }
            }

        }
    });
}

function loadCriteriaslist(techID, parentID, selector) {
    console.log("selector", selector);
    var data = {
        'techID': techID,
        'parentID': parentID
    };

    $.ajax({
        url: 'php_code/get_criteria_list.php',
        method: 'get',
        data: data,
        dataType: 'json',
        success: function (response) {

            var selEl = $('#' + selector);
            selEl.empty();

            if (response.length > 0) {
                $('<option />').text(optionChoose).val(0).appendTo('#' + selector);
                response.forEach(function (item) {
                    console.log(item);
                    $('<option />').text(item.Name).val(item.CriteriumID).attr("data-mID", item.id).appendTo('#' + selector);
                });

                var nn = selEl.data("nn");
                criteriaPosArray[nn] = selEl.val();
                console.log(criteriaPosArray);

                criteriaDataArray[nn] = response;
                console.log('techDataArray:', criteriaDataArray);

                selEl.trigger('change');
            }
        }
    });

    $('#' + selector).empty();
}

function dateformat(d) {
    var mm, dd;
    if (d.getMonth() < 9) {
        mm = "0" + (d.getMonth() + 1);
    } else {
        mm = d.getMonth() + 1;
    }
    if (d.getDate() < 10) {
        dd = "0" + d.getDate();
    } else {
        dd = d.getDate();
    }
    return d.getFullYear() + "-" + mm + "-" + dd;
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function serialDataToObj(data){
    var obj={};
    var spData = data.split("&");
    for(var key in spData)
    {
        //console.log(spData[key]);
        obj[spData[key].split("=")[0]] = spData[key].split("=")[1];
    }
    return obj;
}

function blockSolverSelector(uID = 0){
    if (uID > 0){
        solverSelector.val(uID);
    }
    solverSelector.find('option').attr('disabled', true);
    solverSelector.attr('readonly', true);
}

function unBlockSolverSelector(){
    solverSelector.find('option').removeAttr('disabled');
    solverSelector.removeAttr('readonly');
}

function blockOrgSelector(orgID = 0){
    if (orgID > 0){
        orgSelector.val(orgID);
        loadBranches(orgID, 0, 'filial_id');
    }
    orgSelector.find('option').attr('disabled', true);
    orgSelector.attr('readonly', true);
}

function blockFilialSelector(filID = 0){
    if (filID > 0){
        filSelector.val(filID);
    }
    filSelector.find('option').attr('disabled', true);
    filSelector.attr('readonly', true);
}

function unBlockOrgSelector(){
    orgSelector.find('option').removeAttr('disabled');
    orgSelector.removeAttr('readonly');
}

function unBlockFilialSelector(){
    filSelector.find('option').removeAttr('disabled');
    filSelector.removeAttr('readonly');
}

function getOrganizations(sel_ID) {
//    console.log("org & fil List");
    $.ajax({
        url: '../php_code/get_dropdown_lists.php',
        method: 'post',
        data: {
            'org': 'org'
        },
        dataType: 'json',
        success: function (response) {
//            console.log(response);
            //<!--    organizaciebis chamonatvali -->
            organizationObj = response.org;
            $('<option />').text('აირჩიეთ...').attr('value', '').appendTo('#' + sel_ID);
            organizationObj.forEach(function (item) {
                $('<option />').text(item.OrganizationName).attr('value', item.id).appendTo('#' + sel_ID);
            });

            if($('#currusertype').data('ut') == 'im_owner'){
                blockOrgSelector($('#currusertype').data('org'));
                blockFilialSelector($('#currusertype').data('fil'));
            }

            waitForDropdowns++;
            if (waitForDropdowns == 2) pageIsReady();
        }
    });
}

function loadBranches(orgID, brID, sel_ID) {
    var branches_el_ID = '#' + sel_ID;
    $(branches_el_ID).empty().removeAttr('disabled');

    if (orgID == "" || orgID == "0") {
        $('<option />').text('აირჩიეთ...').attr('value', '0').appendTo(branches_el_ID);
    } else {
        organizationObj.forEach(function (org) {
            if (org.id == orgID) {
                var branches = org.branches;
                if (branches.length != 1) {
                    $('<option />').text('აირჩიეთ...').attr('value', '').appendTo(branches_el_ID);
                }
                branches.forEach(function (item) {
                    $('<option />').text(item.BranchName).attr('value', item.id).appendTo(branches_el_ID);
                });
                if (brID > 0) {
                    $('#filial_id').val(brID);
                }
            }
        });
    }
}

function getCategory(sel_ID) {
    $.ajax({
        url: 'php_code/get_categorys.php',
        method: 'post',
        data: {
            'category': 'true'
        },
        dataType: 'json',
        success: function (response) {
            categoryObj = response.category;
            console.log("categoriebi movida");
            $('<option />').text('აირჩიეთ...').attr('value', '').appendTo('#' + sel_ID);
            categoryObj.forEach(function (item) {
                $('<option />').text(item.name).attr('value', item.ID).appendTo('#' + sel_ID);
            });

            waitForDropdowns++;
            if (waitingItem != undefined){
                fillAccidentForm(waitingItem);
            }
            if (waitForDropdowns == 2) pageIsReady();
        }
    });
}

function loadSubCategory(catID, subID, sel_ID) {
    var sub_cat_sel = '#' + sel_ID;
    $(sub_cat_sel).empty().removeAttr('disabled');

    if (catID == "" || catID == "0") {
        $('<option />').text('აირჩიეთ...').attr('value', '0').appendTo(sub_cat_sel);
    } else {
        categoryObj.forEach(function (category) {
            if (category.ID == catID) {
                var subCat = category.sub_cat;
                if (subCat.length != 1) {
                    $('<option />').text('აირჩიეთ...').attr('value', '').appendTo(sub_cat_sel);
                }
                subCat.forEach(function (item) {
                    $('<option />').text(item.name).attr('value', item.ID).appendTo(sub_cat_sel);
                });
                if (subID > 0) {
                    $(sub_cat_sel).val(subID);
                }
            }
        });
    }
}

function pageIsReady(){}