jQuery(document).ready(function()
{
    let $mo = jQuery;
    let ajaxurl = otpverificationObj.siteURL;
    let nonce = otpverificationObj.nonce;
    let submitSelector = otpverificationObj.submitSelector;
    let formName = otpverificationObj.formname;
    let emailSelector = otpverificationObj.emailselector;
    let phoneSelector = otpverificationObj.phoneSelector
    let txId="";
    let txIdNew =""
    let isValidated = false
    let isEmailResend = false
    let isSecond = false
    let authType = otpverificationObj.authType;
    let isShortEnabled = otpverificationObj.isEnabledShortcode;
    let isRegistered = otpverificationObj.isRegistered;
    const otpEdit = '<input type="text"' +
        'name="edit_otp"' +
        'id="edit_otp"' +
        'placeholder="Enter OTP"' +
        'style="display:none; ">';
    const messageTextMobile='<br><p id="otpmessage"> An OTP will be sent to your Mobile Number</p>';
    const messageTextBoth = ' <p id="otpmessage">An OTP will be sent to your Mobile Number and Email</p>'
    const messageTextEmail = '<br><p id="otpmessage"> An OTP will be sent to your Email ID</p>';
    const sendButton = '<div class="button" ' +
        'id ="otp_send_button">Send OTP</div> ' +
        '<div class="button" ' +
        'id ="timer" ' +
        'style="visibility:hidden;">00</div><br>';

    const phonelabel = '<label for="reg_phone">Phone Number&nbsp;<span class="required">*</span></label>';

    if(isRegistered === 'false')
    {
        const messageNotRegistered = '<p id="registermessage" style="color: red;font-size: 18px;border: red 1px solid;padding: 5px" > miniOrange : Register/Login with miniOrange to Enable 2FA for this Form</p>';
        $mo(emailSelector).after('<br>'+messageNotRegistered);
    }
    else
    if(($mo(formName).length || $mo(submitSelector).length) && isRegistered!==false && isShortEnabled !== 'false')
    {

        function setMessage(message,color) {
            $mo('#otpmessage').text("")
            $mo('#otpmessage').text(message).css('color',color)
        }

        function sendChallenge(authType, phone, email,nonce, ajaxurl) {
            txId = ""
            let timeLeft = 0
            let timerId
            let data =
                {
                    'action'        :   'mo_shortcode',
                    'mo_action'     :   'challenge',
                    'email'         :    email,
                    'phone'         :    phone,
                    'nonce'         :    nonce ,
                    'authTypeSend'  :    authType
                }

            $mo("#otp_send_button").text("Sending")
            $mo.post(ajaxurl, data,function (response)
            {
                if(response === null)
                {
                    setMessage('Contact Site Administrator','red')
                }
                else
                {
                    switch (response.status)
                    {
                        case "SUCCESS":
                            $mo("#edit_otp").css("display", "block")
                            setMessage(response.message, 'green')

                            if(isSecond)
                            {
                                txIdNew = response.txId
                                clearInterval(timerId)
                            }

                            else
                            {
                                txId = response.txId
                                timeLeft = 30
                            }

                            timerId = setInterval(countdown, 1000)

                        function countdown() {
                            if (timeLeft === 0)
                            {
                                clearTimeout(timerId)
                                $mo("#otp_send_button").css("display", "block");
                                $mo("#timer").css("visibility", "hidden");
                                $mo("#otp_send_button").text("Resend");

                            } else {
                                $mo("#timer").css("visibility", "visible");
                                $mo("#otp_send_button").css("display", "none");
                                $mo("#timer").text(timeLeft);
                                timeLeft--;
                            }
                        }
                            break;
                        case "FAILED":
                            setMessage(response.message,'red')
                            break;
                        case "ERROR":
                            setMessage(response.message,'red')
                            break
                    }
                }
            })


        }
        function validateOTP(otp,nonce,phone,txId,email){

            let data =
                {
                    'action'    :  'mo_shortcode',
                    'mo_action' :  'validate',
                    'otp'       :   otp,
                    'nonce'     :   nonce,
                    'mobile'    :   phone,
                    'txId'      :   txId,
                    'email'     :   email
                }

            if(isValidated === false)
            {
                $mo.post(ajaxurl,data,function(response)
                {
                    if(response === null)
                    {
                        setMessage('Error Validating OTP','red')
                        isValidated = false
                    }
                    else
                        switch (response.status)
                        {
                            case "SUCCESS":
                                setMessage(response.message,'green')
                                isValidated = true
                                if(submitSelector === ".ur-submit-button")
                                {
                                    setTimeout(function () {
                                        location.reload()
                                    }, 15000)
                                }
                                $mo(submitSelector).unbind("click").click()
                                return true
                            case "FAILED":
                                setMessage(response.message,'red')
                                $mo(submitSelector).removeAttr("disabled");
                                return false
                            case "ERROR":
                                $mo(submitSelector).removeAttr("disabled");
                                return false
                        }
                })
            }
            else {

            }
        }
        function validateBoth(otp,nonce,phone,txId,email,isFirst) {
            let data =
                {
                    'action'    :  'mo_shortcode',
                    'mo_action' :  'validate',
                    'otp'       :   otp,
                    'nonce'     :   nonce,
                    'mobile'    :   phone,
                    'txId'      :   txId,
                    'email'     :   email
                }

            if(isValidated === false)
            {
                if(isFirst)
                    $mo.post(ajaxurl,data,function(response)
                    {
                        isFirst = false

                        if(response === null)
                        {
                            setMessage('Error Validating OTP','red')
                            isValidated = false
                        }
                        else
                            switch (response.status)
                            {
                                case "SUCCESS":
                                    setMessage(phone+' '+response.message +' '+ 'Sending OTP on '+email,'green')

                                    isValidated = false
                                    isSecond = true

                                    setTimeout(function () {
                                        sendChallenge('email',null,email,nonce,ajaxurl)
                                    },3000)

                                    $mo('#edit_otp').val("")
                                    $mo('#edit_otp').css('placeholder','Enter OTP Sent on your email')
                                    $mo('#reg_phone').after('<br><p style="color:green;"> Phone Number Validated ☑</p>')
                                    $mo("#reg_phone").attr('disabled','true')
                                    $mo(submitSelector).text('Register')

                                    $mo(submitSelector).click(function(e) {
                                        if (isValidated == false)
                                        {
                                            e.preventDefault()
                                            otp = $mo('#edit_otp').val()
                                            validateOTP(otp, nonce, phone, txIdNew, email)
                                        }
                                    })
                                    break;
                                case "FAILED":
                                    setMessage(response.message,'red')
                                    break
                                case "ERROR":
                                    setMessage(response.message,'red')
                                    break
                            }
                    })
            }
            else
            {
                setMessage('Already Validated', 'red')
                jQuery.reload()
            }
        }

        let phone,email,otp;
        switch (authType) {
            case  'phone':
                if (!$mo(phoneSelector).length)
                {
                    const messageNotRegistered = '<p id="phoneFieldLabel" style="color: red;font-size: 18px;border: red 1px solid;padding: 5px" > miniOrange : Phone Field not Found.</p>';
                    $mo(emailSelector).after('<br>'+messageNotRegistered)
                    return
                }
                $mo(phoneSelector).after(messageTextMobile + otpEdit+ sendButton);
                $mo(phoneSelector).intlTelInput({})
                $mo( "#otp_send_button" ).click(function()
                {
                    phone = $mo(phoneSelector).val()
                    phone = phone.replace(/\s+/g, '')
                    email = $mo(emailSelector).val()
                    if(!validatePhone(phone)) {
                        $mo('#otpmessage').text('Invalid Phone Number').css('color','red')
                        return
                    }
                    if(!validateEmail(email)) {
                        $mo('#otpmessage').text('Invalid Email Address').css('color','red')
                        return
                    }
                    isSecond = false
                    sendChallenge(authType,phone,null,nonce,ajaxurl)
                })


                $mo(submitSelector).click(function(e) {
                    e.preventDefault()
                    if(isValidated === false)
                    {
                        otp = $mo('#edit_otp').val()
                        if(!otp)
                        {
                            setMessage("Please Validate Phone first", "red")
                        }
                        else
                            validateOTP(otp, nonce, phone, txId, email)
                    }
                    else
                    {

                    }
                })


                break;

            case  'email':

                if($mo("#reg_passmail").length)
                {
                    $mo("#reg_passmail").css("visibility","hidden")
                    $mo(".clear").remove()
                }

                let a = $mo(emailSelector).attr('class')
                $mo("#edit_otp").addClass(a)

                let b= $mo(submitSelector).attr('class')
                $mo('#otp_send_button').attr('class',b)

                if (!$mo(emailSelector).length)
                {
                    const messageNotRegistered = '<p id="emailFieldLabel" style="color: red;font-size: 18px;border: red 1px solid;padding: 5px" > miniOrange : Email Field '+emailSelector+' not Found. Please check Selector</p>';
                    if($mo(formName).length)
                        $mo(formName).after('<br>'+messageNotRegistered)
                    else if($mo(submitSelector).length)
                        $mo(submitSelector).after('<br>'+messageNotRegistered)
                    return
                }

                $mo(emailSelector).after(messageTextEmail + otpEdit + sendButton)

                $mo( "#otp_send_button" ).click(function() {   email = $mo(emailSelector).val()
                    if(!validateEmail(email)) {
                        $mo('#otpmessage').text('Invalid Email Address').css('color','red')
                        return
                    }
                    isSecond = false
                    sendChallenge(authType,null,email,nonce,ajaxurl)
                })

                $mo(submitSelector).click(function(e)
                {
                    e.preventDefault()
                    if(isValidated === false)
                    {
                        otp = $mo('#edit_otp').val()
                        if(!otp)
                        {
                            setMessage("Please Validate Email first", "red")
                        }
                        else
                            validateOTP(otp, nonce, phone, txId, email)
                    }
                    else
                    {

                    }

                })

                break;

            case  'both':
                $mo(emailSelector).after('<br>'+ messageTextBoth + otpEdit + sendButton);
                $mo(phoneSelector).intlTelInput({})
                if (!$mo(phoneSelector).length)
                {
                    const messageNotRegistered = '<p id="phoneFieldLabel" style="color: red;font-size: 18px;border: red 1px solid;padding: 5px" > miniOrange : Phone Field not Found.</p>';
                    $mo(emailSelector).after('<br>'+messageNotRegistered)
                    return
                }
                $mo( "#otp_send_button" ).click(function() {
                    phone = $mo(phoneSelector).val()
                    phone = phone.replace(/\s+/g, '')
                    email = $mo(emailSelector).val()
                    if (!validatePhone(phone)) {
                        $mo('#otpmessage').text('Invalid Phone Number').css('color', 'red')
                        return
                    }
                    if (!validateEmail(email)) {
                        $mo('#otpmessage').text('Invalid Email Address').css('color', 'red')
                        return
                    }
                    if (!isSecond)
                        sendChallenge('phone', phone, null, nonce, ajaxurl)
                    else
                    {
                        sendChallenge('email', null, email, nonce, ajaxurl)
                        $mo(submitSelector).text('Register')
                    }
                    $mo(submitSelector).text('Validate')
                })



                $mo(submitSelector).click(function(e)
                {
                    e.preventDefault()
                    if(isValidated === false)
                    {
                        email = $mo(emailSelector).val()
                        otp = $mo('#edit_otp').val()
                        if(!otp || !email)
                        {
                            setMessage("Please Validate Email and Phone first", "red")
                        }
                        else
                            validateBoth(otp,nonce,phone,txId,email,true)
                    }
                    else
                    {

                    }

                })
        }

        function validateEmail(email_address) {
            let email_regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i
            if (!email_regex.test(email_address))
            {
                return false
            }
            return true
        }
        function validatePhone(phone)         {
            let intRegex = /[0-9 -()+]+$/;
            if(phone.length < 10 || phone.length == 0 || (!intRegex.test(phone)))
            {
                return false
            }
            return true
        }

    }
    else
    {

    }

});
