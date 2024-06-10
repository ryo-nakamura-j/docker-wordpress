
window.onload = function(){
    if (jQuery('[data-step-title]').length) {
        jQuery('.mo2f-step-title-wrapper').remove();
        jQuery('.mo2f-setup-content').prepend('<div class="mo2f-step-title-wrapper"></div>');
        var counter = 1;
        jQuery('[data-step-title]:not(.hidden)').each(function() {
            var stepLabel = jQuery(this).attr('data-step-title');
            if (jQuery(this).hasClass('active')) {
                jQuery('.mo2f-step-title-wrapper').append(`<span class="mo2f-step-title mo2f-active-step-title"><span>${counter}</span> ${stepLabel}</span>`);
            } else {
                jQuery('.mo2f-step-title-wrapper').append(`<span class="mo2f-step-title"><span>${counter}</span> ${stepLabel}</span>`);
            }
            counter++;
        });
    }
    mo2f_update_step_titles();
    mo2f_toggle_select_roles_and_users();
    jQuery('#mo2f_select_all_roles').click(function(){
        var method = jQuery('input[name="mo2f_select_all_roles"]').val();
        roles_nodelist = document.getElementsByName('mo2f_policy[mo2f-enforce-roles][]');
        if(method == "Select all")
        text = "Deselect all";
        else
        text = "Select all";
        roles_nodelist.forEach(element => {
            jQuery('input[name="mo2f_select_all_roles"]').val(text);
            element.checked = (method == "Select all");
        });
    })
    jQuery('#mo2f-no-grace-period').click(function(){
        const thingToShow=jQuery('#mo2f-use-grace-period').attr('data-unhide-when-checked');
        jQuery(thingToShow).slideUp(200);
    });
    jQuery('#mo2f-use-grace-period').click(function(){
        const thingToShow=jQuery('#mo2f-use-grace-period').attr('data-unhide-when-checked');
        if(jQuery('#mo2f-use-grace-period').is(':checked')){
            jQuery(thingToShow).slideDown(200);
        }
    })
}

function mo2f_change_settings(){
    next_step_settings_nodes = document.getElementsByClassName('mo2f-step-setting-wrapper');
    var step_titles = document.getElementsByClassName('mo2f-step-title');
    for (let index = 0; index < next_step_settings_nodes.length-1; index++) {
        chosen_step = step_titles[index];
        next_step = step_titles[index+1];
        const element = next_step_settings_nodes[index];
        const next_element = next_step_settings_nodes[index+1];
        if(element.classList.contains("active")){
            element.classList.remove('active');
            next_element.classList.add('active');
            chosen_step.classList.remove('mo2f-active-step-title');
            next_step.classList.add('mo2f-active-step-title');
            break;
        }
    }
}

function mo2f_update_step_titles(){
    jQuery('body').on('click', '.mo2f-step-title', function(e) {
        var currentLabel = jQuery(this).text().substr(2);
        var step_titles = document.getElementsByClassName('mo2f-step-title');
        let count = 0;
        jQuery('[data-step-title]:not(.hidden)').each(function() {
            chosen_step = step_titles[count];
            var currentStep = jQuery(this);
            jQuery('[data-step-title]').removeClass('active');
            var stepLabel = jQuery(this).attr('data-step-title');
            if(currentLabel == stepLabel)
            {
                chosen_step.classList.add('mo2f-active-step-title');
            }
            else{
                chosen_step.classList.remove('mo2f-active-step-title');
            }
            jQuery(`[data-step-title="${currentLabel}"]`).addClass('active');
            count++; 
        });
    });
}




function mo2f_toggle_select_roles_and_users(){
    val = jQuery('input[name="mo2f_policy[mo2f-enforcement-policy]"]:checked').val();
    if(val != 'mo2f-certain-roles-only')
    flag = true;
    else if(val != undefined)
    flag = false;
    else
    flag = true;
    if(document.getElementById('mo2f-show-certain-roles-only') != null){
        if(flag)
            document.getElementById('mo2f-show-certain-roles-only').style.display = 'none';
        else
            jQuery('#mo2f-show-certain-roles-only').slideDown(200);
    }
}

jQuery('#mo2f_select_all_roles').click(function(){
    roles_nodelist = document.getElementsByName('mo2f_policy[mo2f-enforce-roles][]');
    roles_nodelist.forEach(element => {
        element.checked = true;
    });
})






