"use strict";

;
/* global wppopups_builder, wp */

(function ($) {
  var s,
      $builder,
      elements = {};
  var WPPopupsBuilder = {
    settings: {
      form: $('#wppopups-builder-popup'),
      //spinner: '<i class="fa fa-spinner fa-spin"></i>',
      spinner: '<i class="fa fa-circle-o-notch fa-spin wppopups-button-icon" />',
      spinnerInline: '<i class="fa fa-spinner fa-spin wppopups-loading-inline"></i>',
      pagebreakTop: false,
      pagebreakBottom: false,
      upload_img_modal: false
    },

    /**
     * Start the engine.
     *
     * @since 2.0.0
     */
    init: function init() {
      window.wppopups_panel_switch = true;
      s = this.settings; // Document ready

      $(document).ready(WPPopupsBuilder.ready); // Page load

      $(window).on('load', WPPopupsBuilder.load);
    },

    /**
     * Page load.
     *
     * @since 2.0.0
     */
    load: function load() {
      // Remove Loading overlay
      $('#wppopups-builder-overlay').fadeOut(); // Maybe display informational informational modal

      if (wppopups_builder_vars.template_modal_display == '1' && 'fields' == wpp.getQueryString('view')) {
        $.alert({
          title: wppopups_builder_vars.template_modal_title,
          content: wppopups_builder_vars.template_modal_msg,
          icon: 'fa fa-info-circle',
          type: 'blue',
          buttons: {
            confirm: {
              text: wppopups_builder_vars.close,
              btnClass: 'btn-confirm',
              keys: ['enter']
            }
          }
        });
      }
    },

    /**
     * Document ready.
     *
     * @since 2.0.0
     */
    ready: function ready() {
      // Cache builder element.
      $builder = $('#wppopups-builder'); // Cache other elements.

      elements.$fieldOptions = $('#wppopups-field-options');
      elements.$sortableFieldsWrap = $('.wppopups-field-wrap');
      elements.$noFieldsOptions = $('.wppopups-no-fields-holder .no-fields');
      elements.$noFieldsPreview = $('.wppopups-no-fields-holder .no-fields-preview'); // Bind all actions.

      WPPopupsBuilder.bindUIActions(); // Trigger initial save for new popups

      var newForm = wpp.getQueryString('newform');

      if (newForm) {
        WPPopupsBuilder.formSave(false);
      } // Setup/cache some vars not available before


      s.popupID = $('#wppopups-builder-popup').data('id');
      s.pagebreakTop = $('.wppopups-pagebreak-top').length;
      s.pagebreakBottom = $('.wppopups-pagebreak-bottom').length;
      s.templateList = new List('wppopups-setup-templates-additional', {
        valueNames: ['wppopups-template-name']
      }); // If there is a section configured, display it. Otherwise
      // we show the first panel by default.

      $('.wppopups-panel').each(function (index, el) {
        var $this = $(this);
        window.$configured = $this.find('.wppopups-panel-sidebar-section.configured').first();

        if (window.$configured.length) {
          var section = window.$configured.data('section');
          window.$configured.addClass('active').find('.wppopups-toggle-arrow').toggleClass('fa-angle-down fa-angle-right');
          $this.find('.wppopups-panel-content-section-' + section).show().addClass('active');
        } else {
          $this.find('.wppopups-panel-content-section:first-of-type').show().addClass('active');
          $this.find('.wppopups-panel-sidebar-section:first-of-type').addClass('active').find('.wppopups-toggle-arrow').toggleClass('fa-angle-down fa-angle-right');
        }
      });
      WPPopupsBuilder.fieldSortable();
      WPPopupsBuilder.fieldChoiceSortable('select');
      WPPopupsBuilder.fieldChoiceSortable('radio');
      WPPopupsBuilder.fieldChoiceSortable('checkbox'); // Load match heights

      $('.wppopups-setup-templates.core .wppopups-template-inner').matchHeight({
        byRow: false
      });
      $('.wppopups-setup-templates.additional .wppopups-template-inner').matchHeight({
        byRow: false
      }); // Trim long popup titles

      WPPopupsBuilder.trimPopupTitle(); // Load Tooltips

      WPPopupsBuilder.loadTooltips(); // Load ColorPickers

      WPPopupsBuilder.loadColorPickers(); // Choices for select fields

      WPPopupsBuilder.initChoicesJS(); // Clone form title to setup page

      $('#wppopups-setup-name').val($('#wppopups-panel-field-settings-popup_title').val()); // jquery-confirmd defaults

      jconfirm.defaults = {
        closeIcon: true,
        backgroundDismiss: true,
        escapeKey: true,
        animationBounce: 1,
        useBootstrap: false,
        theme: 'modern',
        boxWidth: '400px',
        animateFromElement: false
      };
      $('[data-depend][data-depend!=""]').each(function (e) {
        var depend_value = $(this).data('depend-value'),
            field_container = $(this).closest('.wppopups-panel-field'),
            dependent_field = $('#' + $(this).data('depend'));
        dependent_field.on('keyup mouseup change', function () {
          var current_value;

          if (dependent_field.is(':checkbox')) {
            if (dependent_field.is(':checked')) current_value = dependent_field.val();else current_value = 0;
          } else current_value = dependent_field.val();

          if (depend_value.toString().indexOf(current_value) != -1) field_container.show();else field_container.hide();
        });
        dependent_field.trigger('change');
      }); // check if trigger

      WPPopupsBuilder.maybeTriggerAutoHide();
      window.wp.hooks.doAction('wppopupsAdminBuilderReady');
    },
    alterClass: function alterClass(element, removals, additions) {
      if (removals.indexOf('*') === -1) {
        // Use native jQuery methods if there is no wildcard matching
        element.removeClass(removals);
        return !additions ? element : element.addClass(additions);
      }

      var patt = new RegExp('\\s' + removals.replace(/\*/g, '[A-Za-z0-9-_]+').split(' ').join('\\s|\\s') + '\\s', 'g');
      element.each(function (i, it) {
        var cn = ' ' + it.className + ' ';

        while (patt.test(cn)) {
          cn = cn.replace(patt, ' ');
        }

        it.className = $.trim(cn);
      });
      return !additions ? element : element.addClass(additions);
    },

    /**
     * Initilize Choices JS elements.
     *
     */
    initChoicesJS: function initChoicesJS() {
      $('.choicesjs-select').each(function () {
        // if already initiated continue
        if (typeof $(this)[0].choices != 'undefined') return true;
        var $this = $(this),
            args = {
          searchEnabled: false
        };

        if ($this.attr('multiple')) {
          args.searchEnabled = true;
          args.removeItemButton = true;
        }

        if ($this.data('placeholder')) {
          args.placeholderValue = $this.data('placeholder');
        }

        if ($this.data('sorting') === 'off') {
          args.shouldSort = false;
        }

        if ($this.data('search')) {
          args.searchEnabled = true;
        }

        $(this)[0].choices = new Choices($this[0], args);
      });
    },

    /**
     * Element bindings.
     *
     * @since 2.0.0
     */
    bindUIActions: function bindUIActions() {
      // General Panels
      WPPopupsBuilder.bindUIActionsPanels(); // Setup Panel

      WPPopupsBuilder.bindUIActionsSetup(); // Fields Panel

      WPPopupsBuilder.bindUIActionsFields(); // Content Panel

      WPPopupsBuilder.bindUIActionsContent(); // Appearance Panel

      WPPopupsBuilder.bindUIActionsAppearance(); // Settings Panel

      WPPopupsBuilder.bindUIActionsSettings(); // Rules

      WPPopupsBuilder.bindUIActionsRules(); // Optin Forms

      WPPopupsBuilder.optinUIActions(); // Providers

      WPPopupsBuilder.providersUIActions(); // Save and Exit

      WPPopupsBuilder.bindUIActionsSaveExit();
    },
    // --------------------------------------------------------------------//
    // General Panels
    // --------------------------------------------------------------------//

    /**
     * Optin form ui actions
     */
    optinUIActions: function optinUIActions() {
      // Disable form submission in builder
      $('.spu-optin-form').on('submit', function (e) {
        e.preventDefault();
        return false;
      }); // Form class
      //$('#wppopups-panel-field-optin_styles-optin_form_css').on( 'keyup', function (e) {
      //   $('.spu-optin-form').prop('class','spu-optin-form ' + $(this).val());
      //});
      // Submit button

      $('#wppopups-panel-field-optin_styles-submit_text').on('keyup', function (e) {
        $('.wppopups-submit-button').val($(this).val());
      }); // Submit button class
      //$('#wppopups-panel-field-optin_styles-submit_class').on( 'keyup', function (e) {
      //    $('.wppopups-submit-button').prop('class','wppopups-submit-button ' + $(this).val());
      //});
      // Display GDPR field

      $('#wppopups-panel-field-fields-gdpr_field').on('change', function (e) {
        var display_gdpr = $(this).val(),
            $input_gdpr = $('.spu-fields.spu-gdpr');

        if (display_gdpr == '1') {
          if ($input_gdpr.length == 0) {
            WPPopupsBuilder.gdprField().insertBefore('.spu-fields.spu-submit');
          }
        } else {
          $('.spu-fields.spu-gdpr').remove();
        }
      }); // GDPR text

      $('#wppopups-panel-field-fields-gdpr_field_text').on('keyup', function (e) {
        $('.spu-fields.spu-gdpr').replaceWith(WPPopupsBuilder.gdprField());
      }); // GDPR url

      $('#wppopups-panel-field-fields-gdpr_url').on('keyup', function (e) {
        $('.spu-fields.spu-gdpr').replaceWith(WPPopupsBuilder.gdprField());
      }); // Submit BG color

      $('#wppopups-panel-field-optin_styles-submit_bg_color').on('change', function (e) {
        $('.wppopups-submit-button').css('background-color', $(this).val());
      }); // Submit BG color

      $('#wppopups-panel-field-optin_styles-submit_text_color').on('change', function (e) {
        $('.wppopups-submit-button').css('color', $(this).val());
      }); // Submit Border color

      $('#wppopups-panel-field-optin_styles-submit_bg_color').on('change', function (e) {
        $('.wppopups-submit-button').css('background-color', $(this).val());
      });
      $('.wppopups-submit-button').hover(function () {
        $('.wppopups-submit-button').css('background-color', $('#wppopups-panel-field-optin_styles-submit_bg_color_hover').val());
      }, function (e) {
        $('.wppopups-submit-button').css('background-color', $('#wppopups-panel-field-optin_styles-submit_bg_color').val());
      }); // Submit border color

      $('#wppopups-panel-field-optin_styles-submit_border_color').on('change', function (e) {
        $('.wppopups-submit-button').css('border-color', $(this).val());
      }); // Require email

      $('#wppopups-field-option-0-required').on('click', function () {
        $(this).prop('checked', true);
      });
    },

    /**
     * Optin name field
     * @returns {*|jQuery|HTMLElement}
     */
    optinNameField: function optinNameField() {
      var $placeholder = $('#wppopups-panel-field-fields-name_field_text').val();
      return $('<input type="text" name="spu-name" class="spu-fields spu-name" placeholder="' + $placeholder + '"/>');
    },

    /**
     * GDPR field
     * @returns {*|jQuery|HTMLElement}
     */
    gdprField: function gdprField() {
      var $url = $('#wppopups-panel-field-fields-gdpr_url').val();
      var $text = $('#wppopups-panel-field-fields-gdpr_field_text').val();

      if ($url.length) {
        $text = '<a href="' + $url + '" target="_blank" rel="nofollow">' + $text + '</a>';
      }

      return $('<label class="spu-fields spu-gdpr"><input type="checkbox" name="gdpr" value="1" />' + $text + '</label>');
    },

    /**
     * UI Actions for providers tab
     */
    providersUIActions: function providersUIActions() {
      // Delete connection.
      $(document).on('click', '.wppopups-provider-connection-delete', function (e) {
        WPPopupsBuilder.connectionDelete(this, e);
      }); // Add new connection.

      $(document).on('click', '.wppopups-provider-connections-add', function (e) {
        WPPopupsBuilder.connectionAdd(this, e);
      }); // Add new provider account.

      $(document).on('click', '.wppopups-provider-account-add button', function (e) {
        WPPopupsBuilder.accountAdd(this, e);
      }); // Select provider account.

      $(document).on('change', '.wppopups-provider-accounts select', function (e) {
        WPPopupsBuilder.accountSelect(this, e);
      }); // Select account list.

      $(document).on('change', '.wppopups-provider-lists select', function (e) {
        WPPopupsBuilder.accountListSelect(this, e);
      });
      $(document).on('wppopupsBeforePanelSwitch', function (e, targetPanel, currentPanel) {
        WPPopupsBuilder.providerPanelConfirm(targetPanel, currentPanel);
      }); //Extra Options.

      $(document).on('click', '.wppopups-addon-options-save', function (e) {
        WPPopupsBuilder.addonSave(this, e);
      }); //Save connection.

      $(document).on('click', '.wppopups-provider-connections-save', function (e) {
        WPPopupsBuilder.connectionSave(this, e);
      });
    },

    /**
     * Delete provider connection
     *
     * @since 2.0.0
     */
    connectionDelete: function connectionDelete(el, e) {
      e.preventDefault();
      var $this = $(el);
      $.confirm({
        title: false,
        content: wppopups_builder_providers.confirm_connection,
        backgroundDismiss: false,
        closeIcon: false,
        icon: 'fa fa-exclamation-circle',
        type: 'orange',
        buttons: {
          confirm: {
            text: wppopups_builder_vars.ok,
            btnClass: 'btn-confirm',
            keys: ['enter'],
            action: function action() {
              $this.closest('.wppopups-provider-connection').remove();
              $(document).trigger('wppopups_connection_delete', [$this]);
            }
          },
          cancel: {
            text: wppopups_builder_vars.cancel
          }
        }
      });
    },

    /**
     * Add new provider connection.
     *
     * @since 2.0.0
     */
    connectionAdd: function connectionAdd(el, e) {
      e.preventDefault();
      var $this = $(el),
          $connections = $this.parent().parent(),
          $container = $this.parent(),
          provider = $this.data('provider'),
          type = $this.data('type'),
          namePrompt = wppopups_builder_providers.prompt_connection,
          nameField = '<input autofocus="" type="text" id="provider-connection-name" placeholder="' + wppopups_builder_providers.prompt_placeholder + '">',
          nameError = '<p class="error">' + wppopups_builder_providers.error_name + '</p>',
          modalContent = namePrompt + nameField + nameError;
      modalContent = modalContent.replace(/%type%/g, type);
      $.confirm({
        title: false,
        content: modalContent,
        icon: 'fa fa-info-circle',
        type: 'blue',
        backgroundDismiss: false,
        closeIcon: false,
        buttons: {
          confirm: {
            text: wppopups_builder_vars.ok,
            btnClass: 'btn-confirm',
            keys: ['enter'],
            action: function action() {
              var input = this.$content.find('input#provider-connection-name');
              var error = this.$content.find('.error');

              if (input.val() === '') {
                error.show();
                return false;
              } else {
                var name = input.val(); // Disable button.

                WPPopupsBuilder.inputToggle($this, 'disable'); // Fire AJAX.

                var data = {
                  action: 'wppopups_provider_ajax_' + provider,
                  provider: provider,
                  task: 'new_connection',
                  name: name,
                  id: s.form.data('id'),
                  nonce: wppopups_builder_vars.nonce
                };
                WPPopupsBuilder.fireAJAX($this, data, function (res) {
                  if (res.success) {
                    $connections.find('.wppopups-provider-connections').prepend(res.data.html); // Process and load the accounts if they exist

                    var $connection = $connections.find('.wppopups-provider-connection:first');

                    if ($connection.find('.wppopups-provider-accounts option:selected')) {
                      $connection.find('.wppopups-provider-accounts option:first').prop('selected', true);
                      $connection.find('.wppopups-provider-accounts select').trigger('change');
                    }
                  } else {
                    WPPopupsBuilder.errorDisplay(res.data.error, $container);
                  }

                  $(document).trigger('wppopups_connection_add', [res, $this]);
                });
              }
            }
          },
          cancel: {
            text: wppopups_builder_vars.cancel
          }
        }
      });
    },

    /**
     * Add and authorize provider account.
     *
     * @since 2.0.0
     */
    accountAdd: function accountAdd(el, e) {
      e.preventDefault();
      var $this = $(el),
          provider = $this.data('provider'),
          $connection = $this.closest('.wppopups-provider-connection'),
          $container = $this.parent(),
          $fields = $container.find(':input'),
          errors = WPPopupsBuilder.requiredCheck($fields, $container); // Disable button.

      WPPopupsBuilder.inputToggle($this, 'disable'); // Bail if we have any errors.

      if (errors) {
        $this.prop('disabled', false).find('i').remove();
        return false;
      } // Fire AJAX.


      var data = {
        action: 'wppopups_provider_ajax_' + provider,
        provider: provider,
        connection_id: $connection.data('connection_id'),
        task: 'new_account',
        data: WPPopupsBuilder.fakeSerialize($fields)
      };
      WPPopupsBuilder.fireAJAX($this, data, function (res) {
        if (res.success) {
          $container.nextAll('.wppopups-connection-block').remove();
          $container.nextAll('.wppopups-conditional-block').remove();
          $container.after(res.data.html);
          $container.slideUp();
          $connection.find('.wppopups-provider-accounts select').trigger('change');
        } else {
          WPPopupsBuilder.errorDisplay(res.data.error, $container);
        }
      });
    },

    /**
     * Selecting a provider account
     *
     * @since 2.0.0
     */
    accountSelect: function accountSelect(el, e) {
      e.preventDefault();
      var $this = $(el),
          $connection = $this.closest('.wppopups-provider-connection'),
          $container = $this.parent(),
          provider = $connection.data('provider'); // Disable select, show loading.

      WPPopupsBuilder.inputToggle($this, 'disable'); // Remove any blocks that might exist as we prep for new account.

      $container.nextAll('.wppopups-connection-block').remove();
      $container.nextAll('.wppopups-conditional-block').remove();

      if (!$this.val()) {
        // User selected to option to add new account.
        $connection.find('.wppopups-provider-account-add input').val('');
        $connection.find('.wppopups-provider-account-add').slideDown();
        WPPopupsBuilder.inputToggle($this, 'enable');
      } else {
        $connection.find('.wppopups-provider-account-add').slideUp(); // Fire AJAX.

        var data = {
          action: 'wppopups_provider_ajax_' + provider,
          provider: provider,
          connection_id: $connection.data('connection_id'),
          task: 'select_account',
          account_id: $this.find(':selected').val()
        };
        WPPopupsBuilder.fireAJAX($this, data, function (res) {
          if (res.success) {
            $container.after(res.data.html); // Process first list found.

            $connection.find('.wppopups-provider-lists option:first').prop('selected', true);
            $connection.find('.wppopups-provider-lists select').trigger('change');
          } else {
            WPPopupsBuilder.errorDisplay(res.data.error, $container);
          }
        });
      }
    },

    /**
     * Selecting a provider account list.
     *
     * @since 2.0.0
     */
    accountListSelect: function accountListSelect(el, e) {
      e.preventDefault();
      var $this = $(el),
          $connection = $this.closest('.wppopups-provider-connection'),
          $container = $this.parent(),
          provider = $connection.data('provider'); // Disable select, show loading.

      WPPopupsBuilder.inputToggle($this, 'disable'); // Remove any blocks that might exist as we prep for new account.

      $container.nextAll('.wppopups-connection-block').remove();
      $container.nextAll('.wppopups-conditional-block').remove();
      var data = {
        action: 'wppopups_provider_ajax_' + provider,
        provider: provider,
        connection_id: $connection.data('connection_id'),
        task: 'select_list',
        account_id: $connection.find('.wppopups-provider-accounts option:selected').val(),
        list_id: $this.find(':selected').val(),
        popup_id: s.popupID
      };
      WPPopupsBuilder.fireAJAX($this, data, function (res) {
        if (res.success) {
          $container.after(res.data.html);
        } else {
          WPPopupsBuilder.errorDisplay(res.data.error, $container);
        }
      });
    },

    /**
     * Confirm form save before loading Provider panel.
     * If confirmed, save and reload panel.
     *
     * @since 2.0.0
     */
    providerPanelConfirm: function providerPanelConfirm(targetPanel, currentPanel) {
      if (targetPanel === 'providers') {
        if (wpp.savedState != wpp.getFormState('#wppopups-builder-popup') || currentPanel === 'providers') {
          $.confirm({
            title: false,
            content: wppopups_builder_providers.confirm_save,
            backgroundDismiss: false,
            closeIcon: false,
            icon: 'fa fa-info-circle',
            type: 'blue',
            buttons: {
              confirm: {
                text: wppopups_builder_vars.ok,
                btnClass: 'btn-confirm',
                keys: ['enter'],
                action: function action() {
                  $('#wppopups-save').trigger('click');
                  $(document).on('wppopupsSaved', function () {
                    window.location.href = wppopups_builder_providers.url + encodeURI('&view=') + targetPanel;
                  });
                }
              },
              cancel: {
                text: wppopups_builder_vars.cancel,
                action: function action() {
                  if (currentPanel === 'providers') {
                    var $panel = $('#wppopups-panel-' + currentPanel),
                        $panelBtn = $('.wppopups-panel-' + currentPanel + '-button');
                    $('#wppopups-panels-toggle').find('button').removeClass('active');
                    $('.wppopups-panel').removeClass('active');
                    $panelBtn.addClass('active');
                    $panel.addClass('active');
                    history.replaceState({}, null, wpp.updateQueryString('view', currentPanel));
                  }
                }
              }
            }
          });
        }
      }
    },

    /**
     * Save new extra options.
     *
     * @since 2.0.0
     */
    addonSave: function addonSave(el, e) {
      e.preventDefault();
      $('#wppopups-save').trigger('click');
      $(document).on('wppopupsSaved', function () {
        window.location.href = wppopups_builder_addons.url;
      });
    },

    /**
     * Save new provider connection.
     *
     * @since 2.0.0
     */
    connectionSave: function connectionSave(el, e) {
      e.preventDefault();
      $('#wppopups-save').trigger('click');
      $(document).on('wppopupsSaved', function () {
        window.location.href = wppopups_builder_providers.url + encodeURI('&view=optin');
      });
    },

    /**
     * Element bindings for general panel tasks.
     *
     * @since 2.0.0
     */
    bindUIActionsPanels: function bindUIActionsPanels() {
      // Panel switching
      $builder.on('click', '#wppopups-panels-toggle button, .wppopups-panel-switch', function (e) {
        e.preventDefault();
        WPPopupsBuilder.panelSwitch($(this).data('panel'));
      }); // Panel sections switching

      $builder.on('click', '.wppopups-panel .wppopups-panel-sidebar-section:not(".not-clickable")', function (e) {
        WPPopupsBuilder.panelSectionSwitch(this, e);
      });
      $builder.on('click', '.wppopups-panel .wppopups-panel-content-wrap .back-section', function (e) {
        WPPopupsBuilder.panelSectionBack(this, e);
      });
    },

    /**
     * Switch Panels.
     *
     * @since 2.0.0
     */
    panelSwitch: function panelSwitch(panel) {
      var $panel = $('#wppopups-panel-' + panel),
          $panelBtn = $('.wppopups-panel-' + panel + '-button');

      if (!$panel.hasClass('active') && !$panelBtn.hasClass('upgrade-modal')) {
        // trigger event with future panel and current panel
        var active_panel = $('#wppopups-panels-toggle .active').data('panel');
        $builder.trigger('wppopupsBeforePanelSwitch', [panel, active_panel]);

        if (!window.wppopups_panel_switch) {
          return false;
        }

        $('#wppopups-panels-toggle').find('button').removeClass('active');
        $('.wppopups-panel').removeClass('active');
        $panelBtn.addClass('active');
        $panel.addClass('active');
        history.replaceState({}, null, wpp.updateQueryString('view', panel));
        $builder.trigger('wppopupsAfterPanelSwitch', [panel, active_panel]);
      }
    },

    /**
     * Switch Panel section.
     *
     * @since 2.0.0
     */
    panelSectionSwitch: function panelSectionSwitch(el, e) {
      e.preventDefault();
      var $this = $(el),
          $panel = $this.parent().parent(),
          $superpa = $panel.parent(),
          superid = $superpa.attr('id'),
          section = $this.data('section'),
          $sectionButtons = $panel.find('.wppopups-panel-sidebar-section'),
          $sectionButton = $panel.find('.wppopups-panel-sidebar-section-' + section),
          allowed_superp = ['wppopups-panel-providers', 'wppopups-panel-settings', 'wppopups-panel-addons'];

      if (!$sectionButton.hasClass('active')) {
        $sectionButtons.removeClass('active');
        $sectionButtons.find('.wppopups-toggle-arrow').removeClass('fa-angle-down').addClass('fa-angle-right');
        $sectionButton.addClass('active');
        $sectionButton.find('.wppopups-toggle-arrow').toggleClass('fa-angle-right fa-angle-down');
        $panel.find('.wppopups-panel-content-section').slideUp();
        $panel.find('.wppopups-panel-content-section-' + section).slideDown();

        if (wppopups_builder_vars.is_mobile && $.inArray(superid, allowed_superp) != -1) {
          $panel.find('.wppopups-panel-content-wrap').css('left', 60);
          $panel.find('.wppopups-panel-content-wrap').prepend('<a href="#" class="back-section">< Back</a>');
        }
      } else {
        // simple toggle open section
        $sectionButton.removeClass('active');
        $sectionButton.find('.wppopups-toggle-arrow').toggleClass('fa-angle-right fa-angle-down');
        $panel.find('.wppopups-panel-content-section').slideUp();
      }
    },

    /**
     * Switch Panel section.
     *
     * @since 2.0.0
     */
    panelSectionBack: function panelSectionBack(el, e) {
      e.preventDefault();
      var $this = $(el),
          $panel = $this.parent();
      $panel.css('left', 695);
      $this.remove();
    },
    // --------------------------------------------------------------------//
    // Setup Panel
    // --------------------------------------------------------------------//

    /**
     * Element bindings for Setup panel.
     *
     * @since 2.0.0
     */
    bindUIActionsSetup: function bindUIActionsSetup() {
      // Focus on the form title field when displaying setup panel
      $(window).on('load', function (e) {
        WPPopupsBuilder.setupTitleFocus(e, wpp.getQueryString('view'));
      }); // Select and apply a template

      $builder.on('click', '.wppopups-template-select', function (e) {
        WPPopupsBuilder.templateSelect(this, e);
      }); // "Blank form" text should trigger template selection

      $builder.on('click', '.wppopups-trigger-blank', function (e) {
        e.preventDefault();
        $('#wppopups-template-blank .wppopups-template-select').trigger('click');
      }); // Keep Setup title and settings title instances the same

      $builder.on('input ', '#wppopups-panel-field-settings-popup_title', function () {
        $('#wppopups-setup-name').val($('#wppopups-panel-field-settings-popup_title').val());
      });
      $builder.on('input', '#wppopups-setup-name', function () {
        $('#wppopups-panel-field-settings-popup_title').val($('#wppopups-setup-name').val());
      }); // Additional template searching

      $builder.on('keyup', '#wppopups-setup-template-search', function () {
        s.templateList.search($(this).val());
      });
    },

    /**
     * Force focus on the form title field when the Setup panel is displaying.
     *
     * @since 2.0.0
     */
    setupTitleFocus: function setupTitleFocus(e, view) {
      if (typeof view !== 'undefined' && view == 'setup') {
        setTimeout(function () {
          $('#wppopups-setup-name').focus();
        }, 100);
      }
    },

    /**
     * Select template.
     *
     * @since 2.0.0
     */
    templateSelect: function templateSelect(el, e) {
      e.preventDefault();
      var $this = $(el),
          $parent = $this.parent().parent();
      var $formName = $('#wppopups-setup-name'),
          $templateBtns = $('.wppopups-template-select'),
          formName = '',
          labelOriginal = $this.html();
      var template = $this.data('template'),
          templateName = $this.data('template-name-raw'),
          title = '',
          action = ''; // Don't do anything for selects that trigger modal

      if ($parent.hasClass('pro-modal')) {
        return;
      } // Disable all template buttons


      $templateBtns.prop('disabled', true); // Display loading indicator

      $this.html(s.spinner + ' ' + wppopups_builder_vars.loading); // This is an existing form

      if (s.popupID) {
        $.confirm({
          title: wppopups_builder_vars.heads_up,
          content: wppopups_builder_vars.template_confirm,
          backgroundDismiss: false,
          closeIcon: false,
          icon: 'fa fa-exclamation-circle',
          type: 'orange',
          buttons: {
            confirm: {
              text: wppopups_builder_vars.ok,
              btnClass: 'btn-confirm',
              action: function action() {
                // Ajax update form
                var data = {
                  title: $formName.val(),
                  action: 'wppopups_update_popup_template',
                  template: template,
                  popup_id: s.popupID,
                  nonce: wppopups_builder_vars.nonce
                };
                $.post(wppopups_builder_vars.ajax_url, data, function (res) {
                  if (res.success) {
                    window.location.href = res.data.redirect;
                  } else {
                    console.log(res);
                  }
                }).fail(function (xhr, textStatus, e) {
                  WPPopupsBuilder.xhrFailed();
                });
              }
            },
            cancel: {
              text: wppopups_builder_vars.cancel,
              action: function action() {
                $templateBtns.prop('disabled', false);
                $this.html(labelOriginal);
              }
            }
          }
        }); // This is a new form
      } else {
        // Check that form title is provided
        if (!$formName.val()) {
          formName = templateName;
        } else {
          formName = $formName.val();
        } // Ajax create new form


        var data = {
          title: formName,
          action: 'wppopups_new_popup',
          template: template,
          popup_id: s.popupID,
          nonce: wppopups_builder_vars.nonce
        };
        $.post(wppopups_builder_vars.ajax_url, data, function (res) {
          if (res.success) {
            window.location.href = res.data.redirect;
          } else {
            console.log(res);
          }
        }).fail(function (xhr, textStatus, e) {
          WPPopupsBuilder.xhrFailed();
        });
      }
    },
    // --------------------------------------------------------------------//
    // Appearance Panel
    // --------------------------------------------------------------------//
    bindUIActionsAppearance: function bindUIActionsAppearance() {
      // Box position
      $('#wppopups-panel-field-position-position').on('change', function (e) {
        var class_name = 'spu-position-' + $(this).val();
        WPPopupsBuilder.alterClass($('.spu-box'), 'spu-position-*', class_name);
      }); // Box animation

      $('#wppopups-panel-field-animation-animation').on('change', function (e) {
        var class_name = 'spu-animation-' + $(this).val();
        $('.spu-box').removeClass('spu-animation-animated');

        if ('fade' == $(this).val()) {
          $('.spu-box').hide().fadeIn();
        }

        if ('slide' == $(this).val()) {
          $('.spu-box').hide().slideDown();
        }

        WPPopupsBuilder.alterClass($('.spu-box'), 'spu-animation-*', class_name).addClass('spu-animation-animated');
      }); // Box width

      $('#wppopups-panel-field-popup_box-width').on('keyup', function (e) {
        $('.spu-box').css('max-width', WPPopupsBuilder.sanitizeSize($(this).val()));
      }); // Box padding

      $('#wppopups-panel-field-popup_box-padding').on('keyup mouseup', function (e) {
        $('.spu-box .spu-container').css('padding', $(this).val() + 'px');
      }); // Border radius

      $('#wppopups-panel-field-popup_box-radius').on('keyup mouseup', function (e) {
        $('.spu-box').css('border-radius', $(this).val() + 'px');
      }); // Box height

      $('#wppopups-panel-field-popup_box-auto_height').on('change', function (e) {
        if ($(this).val() == 'yes') {
          $('.spu-box').css('height', 'auto');
        } else {
          $('.spu-box').css('height', WPPopupsBuilder.sanitizeSize($('#wppopups-panel-field-popup_box-height').val()));
        }
      });
      $('#wppopups-panel-field-popup_box-height').on('keyup', function (e) {
        if ($('#wppopups-panel-field-popup_box-auto_height').val() == 'no') {
          $('.spu-box').css('height', WPPopupsBuilder.sanitizeSize($(this).val()));
        }
      }); // Overlay color

      $('#wppopups-panel-field-colors-overlay_color').on('change', function (e) {
        $('.wppopups-panel-content .spu-bg').css('background-color', $(this).val());
      }); // BG color

      $('#wppopups-panel-field-colors-bg_color').on('change', function (e) {
        $('.spu-box').css('background-color', $(this).val());
      }); // Border type

      $('#wppopups-panel-field-border-border_type').on('change', function (e) {
        $('.spu-box .spu-container').css('border-style', $(this).val());
      }); // Border Color

      $('#wppopups-panel-field-border-border_color').on('change', function (e) {
        $('.spu-box .spu-container').css('border-color', $(this).val());
      }); // Border width

      $('#wppopups-panel-field-border-border_width').on('keyup mouseup', function (e) {
        $('.spu-box .spu-container').css('border-width', $(this).val() + 'px');
      }); // Border radius

      $('#wppopups-panel-field-border-border_radius').on('keyup mouseup', function (e) {
        $('.spu-box .spu-container').css('border-radius', $(this).val() + 'px');
      }); // Border margin

      $('#wppopups-panel-field-border-border_margin').on('keyup mouseup', function (e) {
        $('.spu-box .spu-container').css('margin', $(this).val() + 'px');
        $('.spu-box .spu-container').css('height', 'calc( 100% - ' + parseInt($(this).val()) * 2 + 'px)');
      }); // Shadow

      $('#wppopups-panel-field-shadow-shadow_color,#wppopups-panel-field-shadow-shadow_type,#wppopups-panel-field-shadow-shadow_blur,#wppopups-panel-field-shadow-shadow_x_offset,#wppopups-panel-field-shadow-shadow_y_offset,#wppopups-panel-field-shadow-shadow_spread').on('change mouseup keyup', function (e) {
        WPPopupsBuilder.updateBoxShadow();
      }); // Close color

      $('#wppopups-panel-field-close-close_color').on('change', function (e) {
        $('.spu-box .spu-close').css('color', $(this).val());
      }); // Close hover

      $('.spu-box .spu-close').hover(function () {
        $('.spu-box .spu-close').css('color', $('#wppopups-panel-field-close-close_hover_color').val());
      }, function (e) {
        $('.spu-box .spu-close').css('color', $('#wppopups-panel-field-close-close_color').val());
      }); // Close shadow

      $('#wppopups-panel-field-close-close_shadow_color').on('change', function (e) {
        $('.spu-box .spu-close').css('text-shadow', '2px 3px 2px ' + $(this).val());
      }); // Close size

      $('#wppopups-panel-field-close-close_size').on('keyup mouseup', function (e) {
        $('.spu-box .spu-close').css('font-size', $(this).val() + 'px');
      }); // Close position

      $('#wppopups-panel-field-close-close_position').on('change', function (e) {
        var class_name = 'spu-close-' + $(this).val();
        WPPopupsBuilder.alterClass($('.spu-box .spu-close'), 'spu-close-*', class_name);
      }); // Overlays

      $('#wppopups-panel-field-colors-show_overlay').on('change', function (e) {
        if ($('.spu-bg').length == 0) {
          $('<div class="spu-bg" id="spu-bg-' + s.popupID + '"></div>').insertBefore('.spu-box');
        }

        if ($(this).val() == 'yes-color' || $(this).val() == 'yes') {
          $('.spu-bg').fadeIn();
          $('.spu-bg').removeAttr('style');
          $('.spu-bg').css('background-image', 'none');
        } else if ($(this).val() == 'yes-blur') {
          $('.spu-bg').fadeIn();
          $('.spu-bg').css('background-color', 'transparent');
          $('.spu-bg').css('background-image', '');
          $('#wppopups-panel-field-colors-overlay_blur').change();
        } else {
          $('.spu-bg').hide();
        }
      }); // Overlay Blur

      $('#wppopups-panel-field-colors-overlay_blur').on('change', function (e) {
        var $box_css = $('#wppopups-builder .spu-bg'),
            blur_px = $(this).val() ? 'blur(' + $(this).val() + 'px)' : 'blur(2px)';
        $box_css.css('filter', blur_px).css('webkitFilter', blur_px).css('mozFilter', blur_px).css('oFilter', blur_px).css('msFilter', blur_px);
      }); // Background repeat

      $('#wppopups-panel-field-colors-bg_img_repeat').on('change', function (e) {
        $('.spu-box').css('background-repeat', $(this).val());
      }); // Background size

      $('#wppopups-panel-field-colors-bg_img_size').on('change', function (e) {
        $('.spu-box').css('background-size', $(this).val());
      }); // Upload or add an image.

      $builder.on('click', '.wppopups-image-upload-add', function (event) {
        event.preventDefault();
        var $this = $(this),
            $container = $this.parent(),
            mediaModal;
        mediaModal = wp.media.frames.wppopups_media_frame = wp.media({
          className: 'media-frame wppopups-media-frame',
          frame: 'select',
          multiple: false,
          title: wppopups_builder_vars.upload_image_title,
          library: {
            type: 'image'
          },
          button: {
            text: wppopups_builder_vars.upload_image_button
          }
        });
        mediaModal.on('select', function () {
          var media_attachment = mediaModal.state().get('selection').first().toJSON();
          $container.find('#wppopups-panel-field-colors-bg_img').val(media_attachment.url);
          $('.spu-box').css('background-image', 'url(' + media_attachment.url + ')');
          $container.find('.image-preview').empty();
          $container.find('.image-preview').prepend('<a href="#" title="' + wppopups_builder_vars.upload_image_remove + '" class="wppopups-image-upload-remove"><img src="' + media_attachment.url + '"></a>');

          if ('hide' === $this.data('after-upload')) {
            $this.hide();
          }

          $builder.trigger('wppopupsImageUploadAdd', [$this, $container]);
        }); // Now that everything has been set, let's open up the frame.

        mediaModal.open();
      }); // Remove and uploaded image.

      $builder.on('click', '.wppopups-image-upload-remove', function (event) {
        event.preventDefault();
        var $container = $(this).parent().parent();
        $container.find('.image-preview').empty();
        $container.find('.wppopups-image-upload-add').show();
        $container.find('#wppopups-panel-field-colors-bg_img').val('');
        $('.spu-box').css('background-image', 'none');
        $builder.trigger('wppopupsImageUploadRemove', [$(this), $container]);
      });
    },
    updateBoxShadow: function updateBoxShadow() {
      var $shadow_type = $('#wppopups-panel-field-shadow-shadow_type').val(),
          $shadow_color = $('#wppopups-panel-field-shadow-shadow_color').val(),
          $shadow_blur = $('#wppopups-panel-field-shadow-shadow_blur').val(),
          $shadow_x_offset = $('#wppopups-panel-field-shadow-shadow_x_offset').val(),
          $shadow_y_offset = $('#wppopups-panel-field-shadow-shadow_y_offset').val(),
          $shadow_spread = $('#wppopups-panel-field-shadow-shadow_spread').val();

      if ($shadow_type != 'none') {
        var $box_shadow = ($shadow_type == 'inset' ? 'inset ' : '') + $shadow_x_offset + 'px ' + $shadow_y_offset + 'px ' + $shadow_blur + 'px ' + $shadow_spread + 'px ' + $shadow_color;
        $('.spu-box').css('box-shadow', $box_shadow);
      } else {
        $('.spu-box').css('box-shadow', 'none');
      }
    },
    // --------------------------------------------------------------------//
    // Content Panel
    // --------------------------------------------------------------------//
    bindUIActionsContent: function bindUIActionsContent() {
      // it seems we cannot hook anymore in addeditor so I'm adding this to all registered editors workaround
      if (typeof window.parent.tinymce !== 'undefined' && window.parent.tinymce.EditorManager.editors !== 'undefined') {
        var editors = window.parent.tinymce.EditorManager.editors;

        for (var e in editors) {
          var editor = editors[e];

          if (editor.id != 'wppopups_panel_field_content_popup_content' && editor.id != 'wppopups_panel_field_bottom_content_bottom_content') {
            continue;
          }

          editor.on('change', function () {
            WPPopupsBuilder.updateEditorContent(this);
          });
          editor.on('input', function () {
            WPPopupsBuilder.updateEditorContent(this);
          });
        }
      }

      if (typeof window.parent.tinymce !== 'undefined') {
        window.parent.tinymce.on('addeditor', function (event) {
          var editor = event.editor; //only for content editor

          if (editor.id != 'wppopups_panel_field_content_popup_content' && editor.id != 'wppopups_panel_field_bottom_content_bottom_content') {
            return;
          }

          editor.on('change', function () {
            WPPopupsBuilder.updateEditorContent(this);
          });
          editor.on('input', function () {
            WPPopupsBuilder.updateEditorContent(this);
          });
        }, true);
      }
    },
    updateEditorContent: function updateEditorContent(editor) {
      var fields_container = $('.spu-content').find('.wppopups-field-container');
      var addon_container = $('.spu-content').find('.spu-addon-container');
      var html = editor.getContent();

      if (fields_container && fields_container.length) {
        if (editor.id == 'wppopups_panel_field_content_popup_content') {
          html = html + fields_container[0].outerHTML + window.parent.tinymce.get('wppopups_panel_field_bottom_content_bottom_content').getContent();
        } else {
          html = window.parent.tinymce.get('wppopups_panel_field_content_popup_content').getContent() + fields_container[0].outerHTML + html;
        }
      }

      if (addon_container && addon_container.length) {
        html = html + addon_container[0].outerHTML;
      }

      if (html) {
        $('.spu-content').html(html);
      }
    },
    // --------------------------------------------------------------------//
    // Settings Panel
    // --------------------------------------------------------------------//

    /**
     * Element bindings for Settings panel.
     *
     * @since 2.0.0
     */
    bindUIActionsSettings: function bindUIActionsSettings() {
      // Clicking form title/desc opens Settings panel
      $builder.on('click', '.wppopups-title-desc, .wppopups-field-submit-button, .wppopups-center-popup-name', function (e) {
        e.preventDefault();
        WPPopupsBuilder.panelSwitch('settings');
      });
      $builder.on('click', '.wppopups-panel:not(#wppopups-panel-optin) .wppopups-submit-button', function (e) {
        e.preventDefault();
      });
      $builder.on('click', '#wppopups-panel-optin .wppopups-submit-button', function (e) {
        e.preventDefault();
        var $submit_button = $(this),
            submit_text = $submit_button.val(),
            submit_style = $submit_button.prop('style'),
            $submit_text_processing = $builder.find('#wppopups-panel-field-optin_styles-submit_text_processing'),
            $submit_class_processing = $builder.find('#wppopups-panel-field-optin_styles-submit_class');
        $submit_button.val($submit_text_processing.val());
        $submit_button.prop('style', '');
        $submit_button.prop('class', 'wppopups-submit-button ' + $submit_class_processing.val());
        setTimeout(function () {
          $submit_button.val(submit_text);
          $submit_button.prop('style', submit_style);
          $submit_button.prop('class', 'wppopups-submit-button');
        }, 4000);
      }); // Real-time updates for editing the popup title

      $builder.on('input', '#wppopups-panel-field-settings-popup_title, #wppopups-setup-name', function () {
        var title = $(this).val();

        if (title.length > 38) {
          title = $.trim(title).substring(0, 38).split(" ").slice(0, -1).join(" ") + "...";
        }

        $('.wppopups-popup-name').text(title);
      });
      /** TRIGGERS EVENTS **/
      // change trigger

      $builder.on('change', '.trigger-option .choicesjs-select', function () {
        WPPopupsBuilder.triggersChangeTrigger($(this));
        WPPopupsBuilder.maybeTriggerAutoHide();
      }); // Delete row

      $builder.on('click', '.trigger-actions .remove', function (e) {
        e.preventDefault();
        WPPopupsBuilder.triggersDeleteRow(e, $(this));
      }); // Add row

      $builder.on('click', '.trigger-actions .add', function (e) {
        e.preventDefault();
        WPPopupsBuilder.triggersClone($(this));
      });
    },

    /**
     * Show or hide the auto hide checkbox for triggers
     */
    maybeTriggerAutoHide: function maybeTriggerAutoHide() {
      var show = false;
      $('.choicesjs-select').each(function () {
        if ($(this).val() == 'pixels' || $(this).val() == 'percentage') {
          show = true;
        }
      });

      if (show) {
        $('#wppopups-panel-field-settings-auto_hide-wrap').fadeIn();
      } else {
        $('#wppopups-panel-field-settings-auto_hide-wrap').hide();
      }
    },
    // --------------------------------------------------------------------//
    // Triggers
    // --------------------------------------------------------------------//

    /**
     * Triggers - Change trigger
     *
     * @since 2.0.0
     */
    triggersChangeTrigger: function triggersChangeTrigger(el) {
      var $this = $(el),
          $row = $this.closest('div.trigger-tr'),
          $group = $row.closest('div.trigger-group'),
          $wpp = this;
      this.renderRulesLoading($row, true); // Ajax create new form

      var data = {
        trigger: $this.val(),
        row_key: $row.data('key'),
        group_key: $group.data('key'),
        action: 'wppopups_render_trigger',
        nonce: wppopups_builder_vars.nonce
      };
      $.post(wppopups_builder_vars.ajax_url, data, function (res) {
        if (res.success) {
          $row.find('.trigger-value').html(res.data.trigger_value);
          $wpp.initChoicesJS();
          $wpp.renderRulesLoading($row);
        } else {
          console.log(res);
        }
      }).fail(function (xhr, textStatus, e) {
        WPPopupsBuilder.xhrFailed();
      });
    },

    /**
     * Triggers - Delete row
     *
     * @since 2.0.0
     */
    triggersDeleteRow: function triggersDeleteRow(e, el) {
      var $this = $(el),
          $row = $this.closest('div.trigger-tr'),
          $table = $this.closest('div.trigger-group'),
          total = $table.find('div.trigger-tr').length; // Delete only if one more than row exist

      if (total > '1') {
        $row.remove();
      }
    },

    /**
     * Triggers - Clone
     *
     * @since 2.0.0
     */
    triggersClone: function triggersClone(el) {
      var $this = $(el),
          $row = $this.closest('div.trigger-tr'),
          $new_row = $('.trigger-group-clone.trigger-tr').clone(),
          row_old_id = $row.attr('data-key'),
          row_new_id = 'trigger_' + (parseInt(row_old_id.replace('trigger_', ''), 10) + 1);
      $new_row.attr('data-key', row_new_id);
      $new_row.insertAfter($row);
      $new_row.removeClass('trigger-group-clone').show();
      this.renderRulesLoading($new_row, true);
      $new_row.find('[name]').each(function () {
        // update names
        $(this).attr('name', $(this).attr('name').replace('trigger_id', row_new_id)); // update ids

        $(this).attr('id', $(this).attr('id').replace('trigger_id', row_new_id)); // update classes

        $(this).addClass('choicesjs-select'); // remove disabled attr

        $(this).removeAttr('disabled');
      });
      this.initChoicesJS();
      this.renderRulesLoading($new_row, false);
    },
    // --------------------------------------------------------------------//
    // Save and Exit
    // --------------------------------------------------------------------//

    /**
     * Element bindings for Embed and Save/Exit items.
     *
     * @since 2.0.0
     */
    bindUIActionsSaveExit: function bindUIActionsSaveExit() {
      // Save popup
      $builder.on('click', '#wppopups-save', function (e) {
        e.preventDefault();
        WPPopupsBuilder.popupSave(false);
      }); // Publish popup

      $builder.on('click', '#wppopups-publish', function (e) {
        e.preventDefault();
        WPPopupsBuilder.popupSave(false, true);
      }); // Exit builder

      $builder.on('click', '#wppopups-exit', function (e) {
        e.preventDefault();
        WPPopupsBuilder.popupExit();
      });
    },

    /**
     * Save popup.
     *
     * @since 2.0.0
     */
    popupSave: function popupSave(redirect, publish) {
      var $saveBtn = publish ? $('#wppopups-publish') : $('#wppopups-save'),
          $icon = $saveBtn.find('i'),
          $label = $saveBtn.find('span'),
          text = wppopups_builder_vars.save;

      if (typeof tinyMCE !== 'undefined') {
        tinyMCE.triggerSave();
      }

      $label.text(wppopups_builder_vars.saving);
      $icon.toggleClass('fa-check fa-cog fa-spin');
      var data = {
        action: 'wppopups_save_popup',
        data: JSON.stringify($('#wppopups-builder-popup').serializeArray()),
        id: s.popupID,
        nonce: wppopups_builder_vars.nonce,
        publish: publish ? 1 : 0
      };
      $.post(wppopups_builder_vars.ajax_url, data, function (res) {
        if (res.success) {
          $label.text(text);
          $icon.toggleClass('fa-check fa-cog fa-spin');
          wpp.savedState = wpp.getFormState('#wppopups-builder-popup');
          $builder.trigger('wppopupsSaved');

          if (publish) {
            $saveBtn.remove();
          }

          if (true === redirect) {
            window.location.href = wppopups_builder_vars.exit_url;
          }
        } else {
          alert(res.data.error);
          console.log(res);
        }
      }).fail(function (xhr, textStatus, e) {
        WPPopupsBuilder.xhrFailed();
      });
    },

    /**
     * Exit popup builder.
     *
     * @since 2.0.0
     */
    popupExit: function popupExit() {
      if (WPPopupsBuilder.popupIsSaved()) {
        window.location.href = wppopups_builder_vars.exit_url;
      } else {
        $.confirm({
          title: false,
          content: wppopups_builder_vars.exit_confirm,
          icon: 'fa fa-exclamation-circle',
          type: 'orange',
          backgroundDismiss: false,
          closeIcon: false,
          buttons: {
            confirm: {
              text: wppopups_builder_vars.save_exit,
              btnClass: 'btn-confirm',
              keys: ['enter'],
              action: function action() {
                WPPopupsBuilder.popupSave(true);
              }
            },
            cancel: {
              text: wppopups_builder_vars.exit,
              action: function action() {
                window.location.href = wppopups_builder_vars.exit_url;
              }
            }
          }
        });
      }
    },

    /**
     * Check current popup state.
     *
     * @since 2.0.0
     */
    popupIsSaved: function popupIsSaved() {
      if (wpp.savedState == wpp.getFormState('#wppopups-builder-popup')) {
        return true;
      } else {
        return false;
      }
    },
    // --------------------------------------------------------------------//
    // General / global
    // --------------------------------------------------------------------//

    /**
     * Element bindings for rules
     *
     * @since 2.0.0
     */
    bindUIActionsRules: function bindUIActionsRules() {
      // change rule
      $builder.on('change', '.rule-option .choicesjs-select', function () {
        WPPopupsBuilder.rulesChangeRule($(this));
      }); // Delete row

      $builder.on('click', '.rule-actions .remove', function (e) {
        e.preventDefault();
        WPPopupsBuilder.rulesDeleteRow(e, $(this));
      }); // Add row

      $builder.on('click', '.rule-actions .add', function (e) {
        e.preventDefault();
        WPPopupsBuilder.rulesClone($(this), 'row');
      }); // Add group

      $builder.on('click', '.add-group', function (e) {
        e.preventDefault();
        WPPopupsBuilder.rulesClone($(this), 'group');
      });
    },

    /**
     * Disable/Enable rules fields until ajax it's finished
     * @param $row
     */
    renderRulesLoading: function renderRulesLoading($row, disable) {
      var spinner = ' <i class="fa fa-spinner fa-spin wppopups-loading-inline"></i>';

      if (disable) {
        $('.wppopups-panel-content-section-title').append(spinner);
      } else {
        $('.wppopups-panel-content-section-title .wppopups-loading-inline').remove();
      }

      $row.find('.choicesjs-select').each(function () {
        if (typeof $(this)[0].choices !== 'undefined') {
          if (disable) {
            $(this)[0].choices.disable();
          } else {
            $(this)[0].choices.enable();
          }
        }
      });
      $row.find('input').each(function () {
        if (disable) {
          $(this).prop('disabled', true);
        } else {
          $(this).prop('disabled', false);
        }
      });
    },

    /**
     * Rules - Change rule
     *
     * @since 2.0.0
     */
    rulesChangeRule: function rulesChangeRule(el) {
      var $this = $(el),
          $row = $this.closest('div.rule-tr'),
          $group = $row.closest('div.rule-group'),
          $wpp = this;
      this.renderRulesLoading($row, true); // Ajax create new popup

      var data = {
        rule: $this.val(),
        name: $this.attr('name'),
        row_key: $row.data('key'),
        group_key: $group.data('key'),
        action: 'wppopups_render_rule',
        nonce: wppopups_builder_vars.nonce
      };
      $.post(wppopups_builder_vars.ajax_url, data, function (res) {
        if (res.success) {
          $row.find('.rule-operator').html(res.data.rule_operator);
          $row.find('.rule-value').html(res.data.rule_values);
          $wpp.initChoicesJS();
          $wpp.renderRulesLoading($row);
        } else {
          console.log(res);
        }
      }).fail(function (xhr, textStatus, e) {
        WPPopupsBuilder.xhrFailed();
      });
    },

    /**
     * Rules - Delete row
     *
     * @since 2.0.0
     */
    rulesDeleteRow: function rulesDeleteRow(e, el) {
      var $this = $(el),
          $row = $this.closest('div.rule-tr'),
          $table = $this.closest('div.rule-group'),
          $container_groups = $this.closest('div.wppopups-container-rules'),
          $first_group = $container_groups.find('div.rule-group').first(),
          total = $table.find('div.rule-tr').length; // Delete only if one more than row exist

      if (total > '1') {
        $row.remove();
      } else {
        // if only 1 row but no main table, delete group entirely
        if (!$first_group.is($table)) {
          $table.remove();
        }
      }
    },

    /**
     * Rules - Clone
     *
     * @since 2.0.0
     */
    rulesClone: function rulesClone(el, cloning) {
      var $this = $(el),
          $container_groups = $this.closest('div.wppopups-container-rules'),
          input = $container_groups.attr('data-input'); // if we are cloning group (OR)

      if ('group' == cloning) {
        // our old group is the last group
        var $group = $container_groups.find('div.rule-group').last(),
            $new_group = $('.rule-group-clone').clone(),
            group_old_id = $group.attr('data-key'),
            group_new_id = 'group_' + (parseInt(group_old_id.replace('group_', ''), 10) + 1),
            row_new_id = 'rule_0',
            $new_row = $new_group.find('div.rule-tr');
        $new_row.attr('data-key', row_new_id);
        $new_group.attr('data-key', group_new_id);
        $new_group.removeClass('rule-group-clone');
        $new_group.insertAfter($group).show();
      } else {
        // or we cloning a row (AND)
        var $row = $this.closest('div.rule-tr'),
            $group = $row.closest('div.rule-group'),
            $new_row = $('.rule-group-clone .rule-tr').clone(),
            group_new_id = $group.attr('data-key'),
            row_old_id = $row.attr('data-key'),
            row_new_id = 'rule_' + (parseInt(row_old_id.replace('rule_', ''), 10) + 1);
        $new_row.attr('data-key', row_new_id);
        $new_row.insertAfter($row);
      }

      this.renderRulesLoading($new_row, true);
      $new_row.find('[name]').each(function () {
        // update names
        $(this).attr('name', $(this).attr('name').replace('rule_id', row_new_id));
        $(this).attr('name', $(this).attr('name').replace('group_id', group_new_id));
        $(this).attr('name', $(this).attr('name').replace('clone_rules', input)); // update ids

        $(this).attr('id', $(this).attr('id').replace('rule_id', row_new_id));
        $(this).attr('id', $(this).attr('id').replace('group_id', group_new_id)); // update classes

        $(this).addClass('choicesjs-select'); // remove disabled attr

        $(this).removeAttr('disabled');
      }); // add last-item class
      //  $new_row.find('.rules-td').removeClass('last-item').last().addClass('last-item')

      this.initChoicesJS();
      this.renderRulesLoading($new_row, false);
    },
    //--------------------------------------------------------------------//
    // Fields Panel
    //--------------------------------------------------------------------//

    /**
     * Element bindings for Fields panel.
     *
     * @since 1.0.0
     */
    bindUIActionsFields: function bindUIActionsFields() {
      // Field sidebar tab toggle
      $builder.on('click', '.wppopups-tab a', function (e) {
        e.preventDefault();
        WPPopupsBuilder.fieldTabToggle($(this).parent().attr('id'));
      }); // Form field preview clicking

      $builder.on('click', '.wppopups-field', function (e) {
        WPPopupsBuilder.fieldTabToggle($(this).data('field-id'));
      }); // Field add

      $builder.on('click', '.wppopups-add-fields-button', function (e) {
        e.preventDefault();
        $builder.find('.wppopups-add-fields-button').attr('disabled', 'disabled');
        $builder.find('.wppopups-field').css('opacity', '0.6');
        $(this).append('<i class="fa fa-cog fa-spin" style="float:right;"></i>');
        WPPopupsBuilder.fieldAdd($(this).data('field-type'));
      }); // Field duplicate

      $builder.on('click', '.wppopups-field-duplicate', function (e) {
        e.preventDefault();
        WPPopupsBuilder.fieldDuplicate($(this).parent()); //WPPopupsBuilder.fieldDuplicate($(this).parent().data('field-id'));
      }); // Field delete

      $builder.on('click', '.wppopups-field-delete', function (e) {
        e.preventDefault();
        e.stopPropagation();
        WPPopupsBuilder.fieldDelete($(this).parent().data('field-id'));
      }); // New field choices should be sortable

      $builder.on('wppopupsFieldAdd', function (event, id, type) {
        if (type === 'select' || type === 'radio' || type === 'checkbox' || type === 'payment-multiple' || type === 'payment-checkbox' || type === 'payment-select') {
          WPPopupsBuilder.fieldChoiceSortable(type, '#wppopups-field-option-row-' + id + '-choices ul');
        }
      }); // Field choice add new

      $builder.on('click', '.wppopups-field-option-row-choices .add', function (e) {
        WPPopupsBuilder.fieldChoiceAdd(e, $(this));
      }); // Field choice delete

      $builder.on('click', '.wppopups-field-option-row-choices .remove', function (e) {
        WPPopupsBuilder.fieldChoiceDelete(e, $(this));
      }); // Field choices defaults - before change

      $builder.on('mousedown', '.wppopups-field-option-row-choices input[type=radio]', function (e) {
        var $this = $(this);

        if ($this.is(':checked')) {
          $this.attr('data-checked', '1');
        } else {
          $this.attr('data-checked', '0');
        }
      }); // Field choices defaults

      $builder.on('click', '.wppopups-field-option-row-choices input[type=radio]', function (e) {
        var $this = $(this),
            list = $this.parent().parent();
        $this.parent().parent().find('input[type=radio]').not(this).prop('checked', false);

        if ($this.attr('data-checked') === '1') {
          $this.prop('checked', false);
          $this.attr('data-checked', '0');
        }

        WPPopupsBuilder.fieldChoiceUpdate(list.data('field-type'), list.data('field-id'));
      }); // Field choices update preview area

      $builder.on('change', '.wppopups-field-option-row-choices input[type=checkbox]', function (e) {
        var list = $(this).parent().parent();
        WPPopupsBuilder.fieldChoiceUpdate(list.data('field-type'), list.data('field-id'));
      }); // Field choices display value toggle

      $builder.on('change', '.wppopups-field-option-row-show_values input', function (e) {
        $(this).closest('.wppopups-field-option').find('.wppopups-field-option-row-choices ul').toggleClass('show-values');
      }); // Updates field choices text in almost real time

      $builder.on('focusout', '.wppopups-field-option-row-choices input.label', function (e) {
        var list = $(this).parent().parent();
        WPPopupsBuilder.fieldChoiceUpdate(list.data('field-type'), list.data('field-id'));
      }); // Field Choices Bulk Add

      $builder.on('click', '.toggle-bulk-add-display', function (e) {
        e.preventDefault();
        WPPopupsBuilder.fieldChoiceBulkAddToggle(this);
      });
      $builder.on('click', '.toggle-bulk-add-presets', function (e) {
        e.preventDefault();
        var $presetList = $(this).closest('.bulk-add-display').find('ul');

        if ($presetList.css('display') === 'block') {
          $(this).text(wppopups_builder_vars.bulk_add_presets_show);
        } else {
          $(this).text(wppopups_builder_vars.bulk_add_presets_hide);
        }

        $presetList.slideToggle();
      });
      $builder.on('click', '.bulk-add-preset-insert', function (e) {
        e.preventDefault();
        var $this = $(this),
            preset = $this.data('preset'),
            $container = $this.closest('.bulk-add-display'),
            $presetList = $container.find('ul'),
            $presetToggle = $container.find('.toggle-bulk-add-presets'),
            $textarea = $container.find('textarea');
        $textarea.val('');
        $textarea.insertAtCaret(wppopups_preset_choices[preset].choices.join("\n"));
        $presetToggle.text(wppopups_builder_vars.bulk_add_presets_show);
        $presetList.slideUp();
      });
      $builder.on('click', '.bulk-add-insert', function (e) {
        e.preventDefault();
        WPPopupsBuilder.fieldChoiceBulkAddInsert(this);
      }); // Field Options group toggle

      $builder.on('click', '.wppopups-field-option-group-toggle:not(.upgrade-modal)', function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.parent().toggleClass('wppopups-hide').find('.wppopups-field-option-group-inner').slideToggle();
        $this.find('i').toggleClass('fa-angle-down fa-angle-right');
      }); // REAL TIME
      // Real-time updates for "Show Label" field option

      $builder.on('input', '.wppopups-field-option-row-label input, .wppopups-field-option-row-name input', function (e) {
        var $this = $(this),
            value = $this.val(),
            id = $this.parent().data('field-id');
        $('.wppopups-field-wrap #wppopups-field-' + id).find('.label-title .text').text(value);
      }); // Real-time updates for "Description" field option

      $builder.on('input', '.wppopups-field-option-row-description textarea', function () {
        var $this = $(this),
            value = wpp.sanitizeHTML($this.val()),
            id = $this.parent().data('field-id'),
            $desc = $('.wppopups-field-wrap #wppopups-field-' + id).find('.description');

        if ($desc.hasClass('nl2br')) {
          $desc.html(value.replace(/\n/g, '<br>'));
        } else {
          $desc.html(value);
        }
      }); // Real-time updates for "Required" field option

      $builder.on('change', '.wppopups-field-option-row-required input', function (e) {
        var id = $(this).parent().data('field-id');
        $('.wppopups-field-wrap #wppopups-field-' + id).toggleClass('required');
      }); // Real-time updates for "Confirmation" field option

      $builder.on('change', '.wppopups-field-option-row-confirmation input', function (e) {
        var id = $(this).parent().data('field-id');
        $('.wppopups-field-wrap #wppopups-field-' + id).find('.wppopups-confirm').toggleClass('wppopups-confirm-enabled wppopups-confirm-disabled');
        $('#wppopups-field-option-' + id).toggleClass('wppopups-confirm-enabled wppopups-confirm-disabled');
      }); // Real-time updates for "Size" field option

      $builder.on('change', '.wppopups-field-option-row-size select', function (e) {
        var $this = $(this),
            value = $this.val(),
            id = $this.parent().data('field-id');
        $('.wppopups-field-wrap #wppopups-field-' + id).removeClass('size-small size-medium size-large').addClass('size-' + value);
      }); // Real-time updates for "Placeholder" field option.

      $builder.on('input', '.wppopups-field-option-row-placeholder input', function () {
        var $this = $(this),
            value = wpp.sanitizeHTML($this.val()),
            id = $this.parent().data('field-id'),
            $primary = $('.wppopups-field-wrap #wppopups-field-' + id).find('.primary-input');

        if ($primary.is('select')) {
          if (!value.length) {
            $primary.find('.placeholder').remove();
          } else {
            if ($primary.find('.placeholder').length) {
              $primary.find('.placeholder').text(value);
            } else {
              $primary.prepend('<option class="placeholder" selected>' + value + '</option>');
            }
          }
        } else {
          $primary.attr('placeholder', value);
        }
      }); // Real-time updates for "Confirmation Placeholder" field option

      $builder.on('input', '.wppopups-field-option-row-confirmation_placeholder input', function (e) {
        var $this = $(this),
            value = $this.val(),
            id = $this.parent().data('field-id');
        $('.wppopups-field-wrap #wppopups-field-' + id).find('.secondary-input').attr('placeholder', value);
      }); // Real-time updates for "Hide Label" field option

      $builder.on('change', '.wppopups-field-option-row-label_hide input', function (e) {
        var id = $(this).parent().data('field-id');
        $('.wppopups-field-wrap #wppopups-field-' + id).toggleClass('label_hide');
      }); // Real-time updates for Sub Label visbility field option

      $builder.on('change', '.wppopups-field-option-row-sublabel_hide input', function (e) {
        var id = $(this).parent().data('field-id');
        $('.wppopups-field-wrap #wppopups-field-' + id).toggleClass('sublabel_hide');
      }); // Real-time updates for Date/Time and Name "Format" option

      $builder.on('change', '.wppopups-field-option-row-format select', function (e) {
        var $this = $(this),
            value = $this.val(),
            id = $this.parent().data('field-id');
        $('.wppopups-field-wrap #wppopups-field-' + id).find('.format-selected').removeClass().addClass('format-selected format-selected-' + value);
        $('#wppopups-field-option-' + id).find('.format-selected').removeClass().addClass('format-selected format-selected-' + value);
      }); // Real-time updates specific for Address "Scheme" option

      /*$builder.on('change', '.wppopups-field-option-row-scheme select', function(e) {
          const $this = $(this),
              value = $this.val(),
              id    = $this.parent().data('field-id'),
              $field = $('#wppopups-field-'+id);
           $field.find('.wppopups-address-scheme').addClass('wppopups-hide');
          $field.find('.wppopups-address-scheme-'+value).removeClass('wppopups-hide');
           if ( $field.find('.wppopups-address-scheme-'+value+' .wppopups-country' ).children().length == 0 ) {
              $('#wppopups-field-option-'+id).find('.wppopups-field-option-row-country').addClass('wppopups-hidden');
          } else {
              $('#wppopups-field-option-'+id).find('.wppopups-field-option-row-country').removeClass('wppopups-hidden');
          }
      });*/
      // Real-time updates for Address, Date/Time, and Name "Placeholder" field options

      $builder.on('input', '.wppopups-field-option .format-selected input.placeholder, .wppopups-field-option-address input.placeholder', function (e) {
        var $this = $(this),
            value = $this.val(),
            id = $this.parent().parent().data('field-id'),
            subfield = $this.parent().parent().data('subfield');
        $('.wppopups-field-wrap #wppopups-field-' + id).find('.wppopups-' + subfield + ' input').attr('placeholder', value);
      }); // Toggle Choice Layout advanced field option.

      $builder.on('change', '.wppopups-field-option-row-input_columns select', function () {
        var $this = $(this),
            value = $this.val(),
            cls = '',
            id = $this.parent().data('field-id');

        if (value === '2') {
          cls = 'wppopups-list-2-columns';
        } else if (value === '3') {
          cls = 'wppopups-list-3-columns';
        } else if (value === 'inline') {
          cls = 'wppopups-list-inline';
        }

        $('.wppopups-field-wrap #wppopups-field-' + id).removeClass('wppopups-list-2-columns wppopups-list-3-columns wppopups-list-inline').addClass(cls);
      }); // Toggle the toggle field.

      $builder.on('click', '.wppopups-field-option-row .wppopups-toggle-icon', function (e) {
        var $this = $(this),
            $check = $this.find('input[type=checkbox]'),
            $label = $this.find('.wppopups-toggle-icon-label');
        $this.toggleClass('wppopups-off wppopups-on');
        $this.find('i').toggleClass('fa-toggle-off fa-toggle-on');

        if ($this.hasClass('wppopups-on')) {
          $label.text(wppopups_builder_vars.on);
          $check.prop('checked', true);
        } else {
          $label.text(wppopups_builder_vars.off);
          $check.prop('checked', false);
        }

        $check.trigger('change');
      }); // Toggle Layout selector

      $builder.on('click', '.toggle-layout-selector-display', function (e) {
        e.preventDefault();
        WPPopupsBuilder.fieldLayoutSelectorToggle(this);
      });
      $builder.on('click', '.layout-selector-display-layout', function (e) {
        e.preventDefault();
        WPPopupsBuilder.fieldLayoutSelectorLayout(this);
      });
      $builder.on('click', '.layout-selector-display-columns span', function (e) {
        e.preventDefault();
        WPPopupsBuilder.fieldLayoutSelectorInsert(this);
      });
      $builder.on('change', '.wppopups-field-option-row-limit_enabled input', function (event) {
        WPPopupsBuilder.updateTextFieldsLimitControls($(event.target).parents('.wppopups-field-option-row-limit_enabled').data().fieldId, event.target.checked);
      });
    },

    /**
     * Delete field
     *
     * @since 1.0.0
     */
    fieldDelete: function fieldDelete(id) {
      var $field = $builder.find('.wppopups-field-wrap #wppopups-field-' + id),
          type = $field.data('field-type');

      if ($field.hasClass('no-delete')) {
        $.alert({
          title: wppopups_builder_vars.field_locked,
          content: wppopups_builder_vars.field_locked_msg,
          icon: 'fa fa-info-circle',
          type: 'blue',
          buttons: {
            confirm: {
              text: wppopups_builder_vars.close,
              btnClass: 'btn-confirm',
              keys: ['enter']
            }
          }
        });
      } else {
        $.confirm({
          title: false,
          content: wppopups_builder_vars.delete_confirm,
          backgroundDismiss: false,
          closeIcon: false,
          icon: 'fa fa-exclamation-circle',
          type: 'orange',
          buttons: {
            confirm: {
              text: wppopups_builder_vars.ok,
              btnClass: 'btn-confirm',
              keys: ['enter'],
              action: function action() {
                $('.wppopups-field-wrap #wppopups-field-' + id).fadeOut(400, function () {
                  $(this).remove();
                  $('.wppopups-field-options #wppopups-field-option-' + id).remove();
                  $('.wppopups-field-wrap .wppopups-field, .wppopups-field-wrap .wppopups-title-desc').removeClass('active');
                  WPPopupsBuilder.fieldTabToggle('add-fields');

                  if ($('.wppopups-field-wrap .wppopups-field').length < 1) {
                    elements.$fieldOptions.append(elements.$noFieldsOptions.clone());
                    elements.$sortableFieldsWrap.append(elements.$noFieldsPreview.clone());
                  } // If GDPR


                  if (type == 'gdpr-checkbox') {
                    $('#wppopups-add-fields-gdpr-checkbox').removeAttr('disabled');
                  }

                  $builder.trigger('wppopupsFieldDelete', [id, type]);
                });
              }
            },
            cancel: {
              text: wppopups_builder_vars.cancel
            }
          }
        });
      }
    },

    /**
     * Duplicate field
     *
     * @since 1.2.9
     */
    fieldDuplicate: function fieldDuplicate($field) {
      //const $field = $builder.find( '.wppopups-field-wrap #wppopups-field-'+id ),
      //        type = $field.data('field-type');
      var type = $field.data('field-type'),
          id = $field.data('field-id');

      if ($field.hasClass('no-duplicate')) {
        $.alert({
          title: wppopups_builder_vars.field_locked,
          content: wppopups_builder_vars.field_locked_msg,
          icon: 'fa fa-info-circle',
          type: 'blue',
          buttons: {
            confirm: {
              text: wppopups_builder_vars.close,
              btnClass: 'btn-confirm',
              keys: ['enter']
            }
          }
        });
      } else {
        $.confirm({
          title: false,
          content: wppopups_builder_vars.duplicate_confirm,
          backgroundDismiss: false,
          closeIcon: false,
          icon: 'fa fa-exclamation-circle',
          type: 'orange',
          buttons: {
            confirm: {
              text: wppopups_builder_vars.ok,
              btnClass: 'btn-confirm',
              keys: ['enter'],
              action: function action() {
                var $newField = $field.clone(),
                    $fieldOptions = $('.wppopups-field-options #wppopups-field-option-' + id),
                    newFieldOptions = $fieldOptions.html(),
                    newFieldID = $('#wppopups-field-id').val(),
                    newFieldLabel = $('.wppopups-field-options #wppopups-field-option-' + id + '-label').val() + ' ' + wppopups_builder_vars.duplicate_copy,
                    nextID = Number(newFieldID) + 1,
                    regex_fieldOptionsID = new RegExp('ID #' + id, "g"),
                    regex_fieldID = new RegExp('fields\\[' + id + '\\]', "g"),
                    regex_dataFieldID = new RegExp('data-field-id="' + id + '"', "g"),
                    regex_referenceID = new RegExp('data-reference="' + id + '"', "g"),
                    regex_elementID = new RegExp('\\b(id|for)="wppopups-(.*?)' + id + '(.*?)"', "ig"); // Toggle visibility states

                $field.after($newField);
                $field.removeClass('active');
                $newField.addClass('active').attr({
                  'id': 'wppopups-field-' + newFieldID,
                  'data-field-id': newFieldID
                }); // Various regex to adjust the field options to work with
                // the new field ID

                function regex_elementID_replace(match, p1, p2, p3, offset, string) {
                  return p1 + '="wppopups-' + p2 + newFieldID + p3 + '"';
                }

                newFieldOptions = newFieldOptions.replace(regex_fieldOptionsID, 'ID #' + newFieldID);
                newFieldOptions = newFieldOptions.replace(regex_fieldID, 'fields[' + newFieldID + ']');
                newFieldOptions = newFieldOptions.replace(regex_dataFieldID, 'data-field-id="' + newFieldID + '"');
                newFieldOptions = newFieldOptions.replace(regex_referenceID, 'data-reference="' + newFieldID + '"');
                newFieldOptions = newFieldOptions.replace(regex_elementID, regex_elementID_replace); // Add new field options panel

                $fieldOptions.hide().after('<div class="wppopups-field-option wppopups-field-option-' + type + '" id="wppopups-field-option-' + newFieldID + '" data-field-id="' + newFieldID + '">' + newFieldOptions + '</div>');
                var $newFieldOptions = $('.wppopups-field-options #wppopups-field-option-' + newFieldID); // Copy over values

                $fieldOptions.find(':input').each(function (index, el) {
                  var $this = $(this),
                      name = $this.attr('name');

                  if (!name) {
                    return 'continue';
                  }

                  var newName = name.replace(regex_fieldID, 'fields[' + newFieldID + ']'),
                      type = $this.attr('type');

                  if (type === 'checkbox' || type === 'radio') {
                    if ($this.is(':checked')) {
                      $newFieldOptions.find('[name="' + newName + '"]').prop('checked', true).attr('checked', 'checked');
                    } else {
                      $newFieldOptions.find('[name="' + newName + '"]').prop('checked', false).attr('checked', false);
                    }
                  } else if ($this.is('select')) {
                    if ($this.find('option:selected').length) {
                      var optionVal = $this.find('option:selected').val();
                      $newFieldOptions.find('[name="' + newName + '"]').find('[value="' + optionVal + '"]').prop('selected', true);
                    }
                  } else {
                    if ($this.val() !== '') {
                      $newFieldOptions.find('[name="' + newName + '"]').val($this.val());
                    } else if ($this.hasClass('wppopups-money-input')) {
                      $newFieldOptions.find('[name="' + newName + '"]').val('0.00');
                    }
                  }
                }); // ID adjustments

                $('.wppopups-field-options #wppopups-field-option-' + newFieldID).find('.wppopups-field-option-hidden-id').val(newFieldID);
                $('#wppopups-field-id').val(nextID); // Adjust label to indicate this is a copy

                $('.wppopups-field-options #wppopups-field-option-' + newFieldID + '-label').val(newFieldLabel);
                $newField.find('.label-title .text').text(newFieldLabel); // Fire field add custom event

                $builder.trigger('wppopupsFieldAdd', [newFieldID, type]); // Lastly, update the next ID stored in database

                $.post(wppopups_builder_vars.ajax_url, {
                  form_id: s.formID,
                  nonce: wppopups_builder_vars.nonce,
                  action: 'wppopups_builder_increase_next_field_id'
                });
              }
            },
            cancel: {
              text: wppopups_builder_vars.cancel
            }
          }
        });
      }
    },

    /**
     * Add new field.
     *
     * @since 1.0.0
     */
    fieldAdd: function fieldAdd(type, options) {
      //const $btn = $( '#wppopups-add-fields-' + type );
      var defaults = {
        position: 'bottom',
        placeholder: false,
        scroll: true,
        defaults: false
      };
      options = $.extend({}, defaults, options);
      var data = {
        action: 'wppopups_new_field_' + type,
        id: s.popupID,
        type: type,
        defaults: options.defaults,
        nonce: wppopups_builder_vars.nonce
      };
      return $.post(wppopups_builder_vars.ajax_url, data, function (res) {
        if (res.success) {
          var totalFields = $('#wppopups-panel-optin .wppopups-field').length,
              $preview = $('#wppopups-panel-optin .wppopups-panel-content-wrap'),
              $lastField = $('.wppopups-field').last(),
              $newField = $(res.data.preview),
              $newOptions = $(res.data.options),
              newID = $newField.attr('id');
          $newField.css('display', 'none');

          if (options.placeholder) {
            options.placeholder.remove();
          } // Determine where field gets placed


          if ('bottom' === options.position) {
            if ($lastField.length && $lastField.hasClass('wppopups-field-stick')) {
              // Check to see if the last field we have is configured to
              // be stuck to the bottom, if so add the field above it.
              $('.wppopups-field-wrap').each(function () {
                $(this).children(':eq(' + (totalFields - 1) + ')').before($newField);
              });
              $('#wppopups-panel-optin .wppopups-field-options').children(':eq(' + (totalFields - 1) + ')').before($newOptions);
            } else {
              // Add field to bottom
              $('.wppopups-field-wrap').append($newField);
              $('#wppopups-panel-optin .wppopups-field-options').append($newOptions);
            }

            if (options.scroll) {
              $preview.animate({
                scrollTop: $preview.prop('scrollHeight') - $preview.height()
              }, 1000);
            }
          } else if ('top' === options.position) {
            // Add field to top, scroll to
            $('.wppopups-field-wrap').prepend($newField);
            $('#wppopups-panel-optin .wppopups-field-options').prepend($newOptions);

            if (options.scroll) {
              $preview.animate({
                scrollTop: 0
              }, 1000);
            }
          } else {
            if (options.position === totalFields && $lastField.length && $lastField.hasClass('wppopups-field-stick')) {
              // Check to see if the user tried to add the field at
              // the end BUT the last field we have is configured to
              // be stuck to the bottom, if so add the field above it.
              $('.wppopups-field-wrap').each(function () {
                $(this).children(':eq(' + (totalFields - 1) + ')').before($newField);
              });
              $('#wppopups-panel-optin .wppopups-field-options').children(':eq(' + (totalFields - 1) + ')').before($newOptions);
            } else if ($('#wppopups-panel-optin .wppopups-field-wrap').children(':eq(' + options.position + ')').length) {
              // Add field to a specific location
              $('.wppopups-field-wrap').each(function () {
                $(this).children(':eq(' + options.position + ')').before($newField);
              });
              $('#wppopups-panel-optin .wppopups-field-options').children(':eq(' + options.position + ')').before($newOptions);
            } else {
              // Something's wrong, just add the field. This should never occur.
              $('.wppopups-field-wrap').append($newField);
              $('#wppopups-panel-optin .wppopups-field-options').append($newOptions);
            }
          } // If GDPR


          if (type == 'gdpr-checkbox') {
            $('#wppopups-add-fields-gdpr-checkbox').attr('disabled', 'disabled');
            $builder.find('.wppopups-field-wrap #' + newID).addClass('no-duplicate');
          }

          $builder.find('.wppopups-field-wrap #' + newID).fadeIn();
          $builder.find('.no-fields, .no-fields-preview').remove();
          $('#wppopups-field-id').val(res.data.field.id + 1);
          wpp.initTooltips(); //app.loadColorPickers();

          $builder.trigger('wppopupsFieldAdd', [res.data.field.id, type]);
        } else {
          console.log(res);
        }

        $builder.find('.wppopups-add-fields-button').removeAttr('disabled');
        $builder.find('.wppopups-field').css('opacity', '1');
        $builder.find('.wppopups-add-fields-button .fa-cog').remove();
      }).fail(function (xhr, textStatus, e) {
        console.log(xhr.responseText);
      });
    },

    /**
     * Sortable fields in the builder form preview area.
     *
     * @since 1.0.0
     */
    fieldSortable: function fieldSortable() {
      var fieldOptions = $('#wppopups-panel-optin .wppopups-field-options'),
          fieldReceived = false,
          fieldIndex,
          fieldIndexNew,
          field,
          fieldNew;
      $('#wppopups-panel-optin .wppopups-field-wrap').sortable({
        items: '> .wppopups-field:not(.wppopups-field-stick):not(.no-fields-preview)',
        axis: 'y',
        delay: 100,
        opacity: 0.75,
        start: function start(e, ui) {
          fieldIndex = ui.item.index();
          field = fieldOptions[0].children[fieldIndex];
        },
        stop: function stop(e, ui) {
          fieldIndexNew = ui.item.index();
          fieldNew = fieldOptions[0].children[fieldIndexNew];

          if (fieldIndex < fieldIndexNew) {
            $(fieldNew).after(field);
            $('#wppopups-panel-content .wppopups-field-wrap div.wppopups-field:eq(' + fieldIndex + ')').insertAfter('#wppopups-panel-content .wppopups-field-wrap div.wppopups-field:eq(' + fieldIndexNew + ')');
            $('#wppopups-panel-appearance .wppopups-field-wrap div.wppopups-field:eq(' + fieldIndex + ')').insertAfter('#wppopups-panel-appearance .wppopups-field-wrap div.wppopups-field:eq(' + fieldIndexNew + ')');
          } else {
            $(fieldNew).before(field);
            $('#wppopups-panel-content .wppopups-field-wrap div.wppopups-field:eq(' + fieldIndex + ')').insertBefore('#wppopups-panel-content .wppopups-field-wrap div.wppopups-field:eq(' + fieldIndexNew + ')');
            $('#wppopups-panel-appearance .wppopups-field-wrap div.wppopups-field:eq(' + fieldIndex + ')').insertBefore('#wppopups-panel-appearance .wppopups-field-wrap div.wppopups-field:eq(' + fieldIndexNew + ')');
          }

          $builder.trigger('wppopupsFieldMove', ui);
          fieldReceived = false;
        },
        over: function over(e, ui) {
          var $el = ui.item.first();
          $el.addClass('wppopups-field-dragging');

          if ($el.hasClass('wppopups-field-drag')) {
            var width = $('.wppopups-field').outerWidth() || elements.$sortableFieldsWrap.find('.no-fields-preview').outerWidth();
            $el.addClass('wppopups-field-drag-over').removeClass('wppopups-field-drag-out').css('width', width).css('height', 'auto');
          }
        },
        out: function out(e, ui) {
          var $el = ui.item.first();
          $el.removeClass('wppopups-field-dragging');

          if (!fieldReceived) {
            var width = $el.attr('data-original-width');

            if ($el.hasClass('wppopups-field-drag')) {
              $el.addClass('wppopups-field-drag-out').removeClass('wppopups-field-drag-over').css('width', width).css('left', '').css('top', '');
            }
          }

          $el.css({
            'top': '',
            'left': '',
            'z-index': ''
          });
        },
        receive: function receive(e, ui) {
          fieldReceived = true;
          var pos = $(this).data('ui-sortable').currentItem.index(),
              $el = ui.helper,
              type = $el.attr('data-field-type');
          $el.addClass('wppopups-field-drag-over wppopups-field-drag-pending').removeClass('wppopups-field-drag-out').css('width', '100%');
          $el.append('<i class="fa fa-cog fa-spin"></i>');
          WPPopupsBuilder.fieldAdd(type, {
            position: pos,
            placeholder: $el
          });
        }
      });
      $('.wppopups-add-fields-button').not('.not-draggable').not('.upgrade-modal').not('.warning-modal').not('.education-modal').draggable({
        connectToSortable: '.wppopups-field-wrap',
        delay: 200,
        helper: function helper() {
          var $this = $(this),
              width = $this.outerWidth(),
              text = $this.html(),
              type = $this.data('field-type'),
              $el = $('<div class="wppopups-field-drag-out wppopups-field-drag">');
          $builder.find('.wppopups-add-fields-button').attr('disabled', 'disabled');
          return $el.html(text).css('width', width).attr('data-original-width', width).attr('data-field-type', type);
        },
        revert: function revert() {
          $builder.find('.wppopups-add-fields-button').removeAttr('disabled');
          return true;
        },
        cancel: false,
        scroll: false,
        opacity: 0.75,
        containment: 'document'
      });
    },

    /**
     * Add new field choice
     *
     * @since 1.0.0
     */
    fieldChoiceAdd: function fieldChoiceAdd(event, el) {
      event.preventDefault();
      var $this = $(el),
          $parent = $this.parent(),
          checked = $parent.find('input.default').is(':checked'),
          fieldID = $this.closest('.wppopups-field-option-row-choices').data('field-id'),
          id = $parent.parent().attr('data-next-id'),
          type = $parent.parent().data('field-type'),
          $choice = $parent.clone().insertAfter($parent);
      $choice.attr('data-key', id);
      $choice.find('input.label').val('').attr('name', 'fields[' + fieldID + '][choices][' + id + '][label]');
      $choice.find('input.value').val('').attr('name', 'fields[' + fieldID + '][choices][' + id + '][value]');
      $choice.find('input.source').val('').attr('name', 'fields[' + fieldID + '][choices][' + id + '][image]');
      $choice.find('input.default').attr('name', 'fields[' + fieldID + '][choices][' + id + '][default]').prop('checked', false);
      $choice.find('.preview').empty();
      $choice.find('.wppopups-image-upload-add').show();
      $choice.find('.wppopups-money-input').trigger('focusout');

      if (checked === true) {
        $parent.find('input.default').prop('checked', true);
      }

      id++;
      $parent.parent().attr('data-next-id', id);
      $builder.trigger('wppopupsFieldChoiceAdd');
      WPPopupsBuilder.fieldChoiceUpdate(type, fieldID);
    },

    /**
     * Delete field choice
     *
     * @since 1.0.0
     */
    fieldChoiceDelete: function fieldChoiceDelete(e, el) {
      e.preventDefault();
      var $this = $(el),
          $list = $this.parent().parent(),
          total = $list.find('li').length;

      if (total == '1') {
        $.alert({
          title: false,
          content: wppopups_builder_vars.error_choice,
          icon: 'fa fa-info-circle',
          type: 'blue',
          buttons: {
            confirm: {
              text: wppopups_builder_vars.ok,
              btnClass: 'btn-confirm',
              keys: ['enter']
            }
          }
        });
      } else {
        $this.parent().remove();
        WPPopupsBuilder.fieldChoiceUpdate($list.data('field-type'), $list.data('field-id'));
        $builder.trigger('wppopupsFieldChoiceDelete');
      }
    },

    /**
     * Make field choices sortable.
     *
     * Currently used for select, radio, and checkboxes field types
     *
     * @since 1.0.0
     */
    fieldChoiceSortable: function fieldChoiceSortable(type, selector) {
      selector = typeof selector !== 'undefined' ? selector : '.wppopups-field-option-' + type + ' .wppopups-field-option-row-choices ul';
      $(selector).sortable({
        items: 'li',
        axis: 'y',
        delay: 100,
        opacity: 0.6,
        handle: '.move',
        stop: function stop(e, ui) {
          var id = ui.item.parent().data('field-id');
          WPPopupsBuilder.fieldChoiceUpdate(type, id);
          $builder.trigger('wppopupsFieldChoiceMove', ui);
        },
        update: function update(e, ui) {}
      });
    },

    /**
     * Update field choices in preview area, for the Fields panel.
     *
     * Currently used for select, radio, and checkboxes field types.
     *
     * @since 1.0.0
     */
    fieldChoiceUpdate: function fieldChoiceUpdate(type, id) {
      // Radio, Checkbox, and Payment Multiple/Checkbox use _ template.
      if ('radio' === type || 'checkbox' === type || 'payment-multiple' === type || 'payment-checkbox' === type) {
        var tmpl = wp.template('wppopups-field-preview-checkbox-radio-payment-multiple'),
            data = {
          settings: wpp.getField(id),
          order: wpp.getChoicesOrder(id),
          type: 'radio'
        };

        if ('checkbox' === type || 'payment-checkbox' === type) {
          data.type = 'checkbox';
        }

        $('.wppopups-field-wrap #wppopups-field-' + id).find('ul.primary-input').replaceWith(tmpl(data));
        return;
      }

      var new_choice; // Multiple payment choices are radio buttons.

      if (type === 'payment-multiple') {
        type = 'radio';
      } // Checkbox payment choices are checkboxes.


      if (type === 'payment-checkbox') {
        type = 'checkbox';
      } // Dropdown payment choices are selects.


      if (type === 'payment-select') {
        type = 'select';
      }

      if (type === 'select') {
        $('#wppopups-field-' + id + ' .primary-input option').not('.placeholder').remove();
        new_choice = '<option>{label}</option>';
      } else if (type === 'radio' || type === 'checkbox' || type === 'gdpr-checkbox') {
        type = 'gdpr-checkbox' === type ? 'checkbox' : type;
        $('#wppopups-field-' + id + ' .primary-input li').remove();
        new_choice = '<li><input type="' + type + '" disabled>{label}</li>';
      }

      $('#wppopups-field-option-row-' + id + '-choices .choices-list li').each(function (index) {
        var $this = $(this),
            label = wpp.sanitizeHTML($this.find('input.label').val()),
            selected = $this.find('input.default').is(':checked'),
            choice = $(new_choice.replace('{label}', label));
        $('#wppopups-field-' + id + ' .primary-input').append(choice);

        if (selected === true) {
          switch (type) {
            case 'select':
              choice.prop('selected', 'true');
              break;

            case 'radio':
            case 'checkbox':
              choice.find('input').prop('checked', 'true');
              break;
          }
        }
      });
    },

    /**
     * Field choice bulk add toggling.
     *
     * @since 1.3.7
     */
    fieldChoiceBulkAddToggle: function fieldChoiceBulkAddToggle(el) {
      var $this = $(el),
          $label = $this.closest('label');

      if ($this.hasClass('bulk-add-showing')) {
        // Import details is showing, so hide/remove it
        var $selector = $label.next('.bulk-add-display');
        $selector.slideUp(400, function () {
          $selector.remove();
        });
        $this.find('span').text(wppopups_builder_var.bulk_add_show);
      } else {
        var importOptions = '<div class="bulk-add-display">';
        importOptions += '<p class="heading wppopups-clear">' + wppopups_builder_var.bulk_add_heading + ' <a href="#" class="toggle-bulk-add-presets">' + wppopups_builder_var.bulk_add_presets_show + '</a></p>';
        importOptions += '<ul>';

        for (var key in wppopups_preset_choices) {
          importOptions += '<li><a href="#" data-preset="' + key + '" class="bulk-add-preset-insert">' + wppopups_preset_choices[key].name + '</a></li>';
        }

        importOptions += '</ul>';
        importOptions += '<textarea placeholder="' + wppopups_builder_var.bulk_add_placeholder + '"></textarea>';
        importOptions += '<button class="bulk-add-insert">' + wppopups_builder_var.bulk_add_button + '</button>';
        importOptions += '</div>';
        $label.after(importOptions);
        $label.next('.bulk-add-display').slideDown(400, function () {
          $(this).find('textarea').focus();
        });
        $this.find('span').text(wppopups_builder_var.bulk_add_hide);
      }

      $this.toggleClass('bulk-add-showing');
    },

    /**
     * Field choice bulk insert the new choices.
     *
     * @since 1.3.7
     *
     * @param {object} el DOM element.
     */
    fieldChoiceBulkAddInsert: function fieldChoiceBulkAddInsert(el) {
      var $this = $(el),
          $container = $this.closest('.wppopups-field-option-row'),
          $textarea = $container.find('textarea'),
          $list = $container.find('.choices-list'),
          $choice = $list.find('li:first-of-type').clone().wrap('<div>').parent(),
          choice = '',
          fieldID = $container.data('field-id'),
          type = $list.data('field-type'),
          nextID = Number($list.attr('data-next-id')),
          newValues = $textarea.val().split('\n'),
          newChoices = '';
      $this.prop('disabled', true).html($this.html() + ' ' + s.spinner);
      $choice.find('input.value,input.label').attr('value', '');
      choice = $choice.html();

      for (var key in newValues) {
        if (!newValues.hasOwnProperty(key)) {
          continue;
        }

        var value = wpp.sanitizeHTML(newValues[key]).trim().replace(/"/g, '&quot;'),
            newChoice = choice;
        newChoice = newChoice.replace(/\[choices\]\[(\d+)\]/g, '[choices][' + nextID + ']');
        newChoice = newChoice.replace(/data-key="(\d+)"/g, 'data-key="' + nextID + '"');
        newChoice = newChoice.replace(/value="" class="label"/g, 'value="' + value + '" class="label"'); // For some reasons IE has its own attribute order.

        newChoice = newChoice.replace(/class="label" type="text" value=""/g, 'class="label" type="text" value="' + value + '"');
        newChoices += newChoice;
        nextID++;
      }

      $list.attr('data-next-id', nextID).append(newChoices);
      WPPopupsBuilder.fieldChoiceUpdate(type, fieldID);
      $builder.trigger('wppopupsFieldChoiceAdd');
      WPPopupsBuilder.fieldChoiceBulkAddToggle($container.find('.toggle-bulk-add-display'));
    },

    /**
     * Toggle fields tabs (Add Fields, Field Options.
     *
     * @since 1.0.0
     */
    fieldTabToggle: function fieldTabToggle(id) {
      $('.wppopups-tab a').removeClass('active').find('i').removeClass('fa-angle-down').addClass('fa-angle-right');
      $('#wppopups-panel-optin .wppopups-field, #wppopups-panel-optin .wppopups-title-desc').removeClass('active');

      if (id === 'add-fields') {
        $('#add-fields').find('a').addClass('active').find('i').addClass('fa-angle-down');
        $('.wppopups-field-options').hide();
        $('.wppopups-add-fields').show();
      } else {
        $('#field-options').find('a').addClass('active').find('i').addClass('fa-angle-down');

        if (id === 'field-options') {
          $('.wppopups-field').first().addClass('active');
          id = $('.wppopups-field').first().data('field-id');
        } else {
          $('#wppopups-panel-optin #wppopups-field-' + id).addClass('active');
        }

        $('.wppopups-field-option').hide();
        $('#wppopups-field-option-' + id).show();
        $('.wppopups-add-fields').hide();
        $('.wppopups-field-options').show();
      }
    },

    /**
     * Update limit controls by changing checkbox.
     *
     * @since 1.5.6
     *
     * @param {number} id Field id.
     * @param {bool} checked Whether an option is checked or not.
     */
    updateTextFieldsLimitControls: function updateTextFieldsLimitControls(id, checked) {
      if (!checked) {
        $('#wppopups-field-option-row-' + id + '-limit_controls').addClass('wppopups-hide');
      } else {
        $('#wppopups-field-option-row-' + id + '-limit_controls').removeClass('wppopups-hide');
      }
    },

    /**
     * Check if valid unit in size
     * @param size
     * @returns {string}
     */
    sanitizeSize: function sanitizeSize(size) {
      if (size.indexOf('%') == -1 && size.indexOf('px') == -1 && size.indexOf('em') == -1 && size.indexOf('vh') == -1 && size.indexOf('vw') == -1 && size.indexOf('vmin') == -1 && size.indexOf('vmax') == -1) {
        size = size + 'px';
      }

      return size;
    },
    // --------------------------------------------------------------------//
    // Other functions
    // --------------------------------------------------------------------//
    //--------------------------------------------------------------------//
    // Helper functions.
    //--------------------------------------------------------------------//

    /**
     * Fire AJAX call.
     *
     * @since 2.0.0
     */
    fireAJAX: function fireAJAX(el, d, success) {
      var $this = $(el);
      var data = {
        id: $('#wppopups-builder-popup').data('id'),
        nonce: wppopups_builder_vars.nonce
      };
      $.extend(data, d);
      $.post(wppopups_builder_vars.ajax_url, data, function (res) {
        success(res);
        WPPopupsBuilder.inputToggle($this, 'enable');
      }).fail(function (xhr, textStatus, e) {
        console.log(xhr.responseText);
      });
    },

    /**
     * Toggle input with loading indicator.
     *
     * @since 2.0.0
     */
    inputToggle: function inputToggle(el, status) {
      var $this = $(el);

      if (status === 'enable') {
        if ($this.is('select')) {
          $this.prop('disabled', false).next('i').remove();
        } else {
          $this.prop('disabled', false).find('i').remove();
        }
      } else if (status === 'disable') {
        if ($this.is('select')) {
          $this.prop('disabled', true).after(s.spinner);
        } else {
          $this.prop('disabled', true).prepend(s.spinner);
        }
      }
    },

    /**
     * Field layout selector toggling.
     *
     * @since 1.3.7
     */
    fieldLayoutSelectorToggle: function fieldLayoutSelectorToggle(el) {
      var $this = $(el),
          $label = $this.closest('label'),
          layouts = {
        'layout-1': [{
          'class': 'one-half',
          'data': 'wppopups-one-half wppopups-first'
        }, {
          'class': 'one-half',
          'data': 'wppopups-one-half'
        }],
        'layout-2': [{
          'class': 'one-third',
          'data': 'wppopups-one-third wppopups-first'
        }, {
          'class': 'one-third',
          'data': 'wppopups-one-third'
        }, {
          'class': 'one-third',
          'data': 'wppopups-one-third'
        }],
        'layout-3': [{
          'class': 'one-fourth',
          'data': 'wppopups-one-fourth wppopups-first'
        }, {
          'class': 'one-fourth',
          'data': 'wppopups-one-fourth'
        }, {
          'class': 'one-fourth',
          'data': 'wppopups-one-fourth'
        }, {
          'class': 'one-fourth',
          'data': 'wppopups-one-fourth'
        }],
        'layout-4': [{
          'class': 'one-third',
          'data': 'wppopups-one-third wppopups-first'
        }, {
          'class': 'two-third',
          'data': 'wppopups-two-thirds'
        }],
        'layout-5': [{
          'class': 'two-third',
          'data': 'wppopups-two-thirds wppopups-first'
        }, {
          'class': 'one-third',
          'data': 'wppopups-one-third'
        }],
        'layout-6': [{
          'class': 'one-fourth',
          'data': 'wppopups-one-fourth wppopups-first'
        }, {
          'class': 'one-fourth',
          'data': 'wppopups-one-fourth'
        }, {
          'class': 'two-fourth',
          'data': 'wppopups-two-fourths'
        }],
        'layout-7': [{
          'class': 'two-fourth',
          'data': 'wppopups-two-fourths wppopups-first'
        }, {
          'class': 'one-fourth',
          'data': 'wppopups-one-fourth'
        }, {
          'class': 'one-fourth',
          'data': 'wppopups-one-fourth'
        }],
        'layout-8': [{
          'class': 'one-fourth',
          'data': 'wppopups-one-fourth wppopups-first'
        }, {
          'class': 'two-fourth',
          'data': 'wppopups-two-fourths'
        }, {
          'class': 'one-fourth',
          'data': 'wppopups-one-fourth'
        }]
      };

      if ($this.hasClass('layout-selector-showing')) {
        // Selector is showing, so hide/remove it
        var $selector = $label.next('.layout-selector-display');
        $selector.slideUp(400, function () {
          $selector.remove();
        });
        $this.find('span').text(wppopups_builder_vars.layout_selector_show);
      } else {
        // Create selector options
        var layoutOptions = '<div class="layout-selector-display">';
        layoutOptions += '<p class="heading">' + wppopups_builder_vars.layout_selector_layout + '</p>';

        for (var key in layouts) {
          var layout = layouts[key];
          layoutOptions += '<div class="layout-selector-display-layout">';

          for (var key in layout) {
            layoutOptions += '<span class="' + layout[key]["class"] + '" data-classes="' + layout[key].data + '"></span>';
          }

          layoutOptions += '</div>';
        }

        layoutOptions += '</div>';
        $label.after(layoutOptions);
        $label.next('.layout-selector-display').slideDown();
        $this.find('span').text(wppopups_builder_vars.layout_selector_hide);
      }

      $this.toggleClass('layout-selector-showing');
    },

    /**
     * Field layout selector, selecting a layout.
     *
     * @since 1.3.7
     */
    fieldLayoutSelectorLayout: function fieldLayoutSelectorLayout(el) {
      var $this = $(el),
          $label = $this.closest('label');
      $this.parent().find('.layout-selector-display-layout').not($this).remove();
      $this.parent().find('.heading').text(wppopups_builder_vars.layout_selector_column);
      $this.toggleClass('layout-selector-display-layout layout-selector-display-columns');
    },

    /**
     * Field layout selector, insert into class field.
     *
     * @since 1.3.7
     */
    fieldLayoutSelectorInsert: function fieldLayoutSelectorInsert(el) {
      var $this = $(el),
          $selector = $this.closest('.layout-selector-display'),
          $parent = $selector.parent(),
          $label = $parent.find('label'),
          $input = $parent.find('input[type=text]'),
          classes = $this.data('classes');

      if ($input.val()) {
        classes = ' ' + classes;
      } //$input.insertAtCaret(classes);


      $input.val(classes); // remove list, all done!

      $selector.slideUp(400, function () {
        $selector.remove();
      });
      $label.find('.toggle-layout-selector-display').removeClass('layout-selector-showing');
      $label.find('.toggle-layout-selector-display span').text(wppopups_builder_vars.layout_selector_show);
    },

    /**
     * Display error.
     *
     * @since 2.0.0
     */
    errorDisplay: function errorDisplay(msg, location) {
      location.find('.wppopups-error-msg').remove();
      location.prepend('<p class="wppopups-alert-danger wppopups-alert wppopups-error-msg">' + msg + '</p>');
    },

    /**
     * Check for required fields.
     *
     * @since 2.0.0
     */
    requiredCheck: function requiredCheck(fields, location) {
      var error = false; // Remove any previous errors.

      location.find('.wppopups-alert-required').remove(); // Loop through input fields and check for values.

      fields.each(function (index, el) {
        if ($(el).hasClass('wppopups-required') && $(el).val().length === 0) {
          $(el).addClass('wppopups-error');
          error = true;
        } else {
          $(el).removeClass('wppopups-error');
        }
      });

      if (error) {
        location.prepend('<p class="wppopups-alert-danger wppopups-alert wppopups-alert-required">' + wppopups_builder_providers.required_field + '</p>');
      }

      return error;
    },

    /**
     * Pseudo serializing. Fake it until you make it.
     *
     * @since 2.0.0
     */
    fakeSerialize: function fakeSerialize(els) {
      var fields = els.clone();
      fields.each(function (index, el) {
        if ($(el).data('name')) {
          $(el).attr('name', $(el).data('name'));
        }
      });
      return fields.serialize();
    },

    /**
     * Trim long popup titles.
     *
     * @since 2.0.0
     */
    trimPopupTitle: function trimPopupTitle() {
      var $title = $('.wppopups-center-popup-name');

      if ($title.text().length > 38) {
        var shortTitle = $.trim($title.text()).substring(0, 38).split(" ").slice(0, -1).join(" ") + "...";
        $title.text(shortTitle);
      }
    },

    /**
     * Load or refresh tooltips.
     *
     * @since 2.0.0
     */
    loadTooltips: function loadTooltips() {
      $('.wppopups-help-tooltip').tooltipster({
        contentAsHTML: true,
        position: 'right',
        maxWidth: 300,
        multiple: true
      });
    },

    /**
     * Load or refresh tooltips.
     *
     * @since 2.0.0
     */
    loadColorPickers: function loadColorPickers() {
      $('.wppopups-color-picker').spectrum({
        showAlpha: true,
        showInput: true,
        preferredFormat: "rgb",
        allowEmpty: true,
        move: function move(tinycolor) {
          $(this).val(tinycolor.toRgbString());
        }
      });
    },
    xhrFailed: function xhrFailed() {
      $.alert({
        title: 'Error',
        content: wppopups_builder_vars.xhr_failed,
        icon: 'fa fa-exclamation-circle',
        type: 'red',
        buttons: {
          confirm: {
            text: wppopups_builder_vars.close,
            btnClass: 'btn-confirm',
            keys: ['enter']
          }
        }
      });
    }
  };
  WPPopupsBuilder.init(); // Add to global scope.

  window.wppopups_builder = WPPopupsBuilder;
})(jQuery);