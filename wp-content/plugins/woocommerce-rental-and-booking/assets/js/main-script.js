jQuery(document).ready(function($) {
  /**
   * Configuratin weekend
   *
   * @since 2.0.0
   * @return null
   */
  var offDays = new Array(),
    conditional_data = CALENDAR_DATA.calendar_props.settings.conditions,
    general_data = CALENDAR_DATA.calendar_props.settings.general,
    validation_data = CALENDAR_DATA.calendar_props.settings.validations,
    layout_two_labels = CALENDAR_DATA.calendar_props.settings.layout_two_labels,
    translatedStrings = CALENDAR_DATA.translated_strings,
    markers = CALENDAR_DATA.markers;

  if (conditional_data.weekends != undefined) {
    var offDaysLength = conditional_data.weekends.length;
    for (var i = 0; i < offDaysLength; i++) {
      offDays.push(parseInt(conditional_data.weekends[i]));
    }
  }

  var domain = "";
  months = "";
  weekdays = "";
  if (
    general_data.lang_domain !== false &&
    general_data.months !== false &&
    general_data.weekdays !== false
  ) {
    (domain = general_data.lang_domain),
      (months = general_data.months.split(",")),
      (weekdays = general_data.weekdays.split(","));
  }
  $.datetimepicker.setLocale(domain);

  function calendarInit() {
    /**
     * Configuratin of date picker for pickupdate
     *
     * @since 1.0.0
     * @return null
     */
    $("#pickup-date").change(function(e) {
      $("#pickup-time").val("");
    });

    var final = [];
    if (CALENDAR_DATA.buffer_days) {
      var allDates = CALENDAR_DATA.block_dates.concat(
        CALENDAR_DATA.buffer_days
      );
      final = allDates.filter((v, i, a) => a.indexOf(v) === i);
    }

    var dateTimeOptions = {};
    var datepickerOption = {
      timepicker: false,
      scrollMonth: false,
      dayOfWeekStart: general_data.day_of_week_start
        ? general_data.day_of_week_start
        : 0,
      format: conditional_data.date_format,
      minDate: 0,
      disabledDates: CALENDAR_DATA.block_dates,
      formatDate: conditional_data.date_format,
      onShow: function(ct) {
        $("#dropoff-date").val("");
        this.setOptions({
          minDate: 0,
          disabledDates: final
        });
      },
      disabledWeekDays: offDays,
      i18n: {
        domain: {
          months: months,
          dayOfWeek: weekdays
        }
      },
      scrollInput: false
    };

    if (window.innerWidth <= 480) {
      $("#pickup-date").datetimepicker("destroy");
      $("#pickup-date").on("click", function() {
        $("#pickup-modal-body ").show();
        $("#mobile-datepicker").datetimepicker({
          scrollMonth: false,
          inline: true,
          timepicker: false,
          minDate: 0,
          dayOfWeekStart: general_data.day_of_week_start
            ? general_data.day_of_week_start
            : 0,
          disabledWeekDays: offDays,
          i18n: {
            domain: {
              months: months,
              dayOfWeek: weekdays
            }
          },
          onShow: function(ct) {
            $("#dropoff-date").val("");
            this.setOptions({
              minDate: 0,
              disabledDates: final
            });
          },
          format: conditional_data.date_format,
          disabledDates: CALENDAR_DATA.block_dates,
          formatDate: conditional_data.date_format,
          onSelectDate: function(ct) {
            dateTimeOptions["date"] = ct;
          },
          onSelectTime: function(ct) {
            dateTimeOptions["time"] = ct;
          }
        });
        $("#cal-close-btn").on("click", function() {
          $("#pickup-modal-body ").hide();
          $("#pickup-date").datetimepicker("destroy");
        });
        $("#cal-submit-btn").on("click", function() {
          $("#pickup-modal-body ").hide();
          $("#pickup-date").datetimepicker(
            Object.assign(datepickerOption, {
              value: dateTimeOptions["date"]
            })
          );
          $("#pickup-time").datetimepicker({
            format:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
            formatTime:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
            value: dateTimeOptions["time"]
          });
          $("form.cart").trigger("change");
          $("#pickup-date").datetimepicker("destroy");
        });
      });
    } else {
      $("#pickup-date").datetimepicker(datepickerOption);
    }

    /**
     * Configuratin of time picker for pickuptime
     *
     * @since 1.0.0
     * @return null
     */
    var OpeningClosingTimeLogic = function(currentDateTime) {
      var euroFormat = conditional_data.euro_format,
        selectedDay;

      if (euroFormat === "yes") {
        var date = $("#pickup-date").val();
        var splitDate = date.split("/");
        var finalDate = `${splitDate[1]}/${splitDate[0]}/${splitDate[2]}`;
        selectedDay = new Date(finalDate).getDay();
      } else {
        selectedDay = new Date($("#pickup-date").val()).getDay();
      }

      switch (selectedDay) {
        case 0:
          this.setOptions({
            minTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.sun.min
                : timeConvert(validation_data.openning_closing.sun.min),
            maxTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.sun.max
                : timeConvert(validation_data.openning_closing.sun.max),
            format:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
            formatTime:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a"
          });
          break;
        case 1:
          this.setOptions({
            minTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.mon.min
                : timeConvert(validation_data.openning_closing.mon.min),
            maxTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.mon.max
                : timeConvert(validation_data.openning_closing.mon.max),
            format:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
            formatTime:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a"
          });
          break;
        case 2:
          this.setOptions({
            minTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.thu.min
                : timeConvert(validation_data.openning_closing.thu.min),
            maxTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.thu.max
                : timeConvert(validation_data.openning_closing.thu.max),
            format:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
            formatTime:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a"
          });
          break;
        case 3:
          this.setOptions({
            minTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.wed.min
                : timeConvert(validation_data.openning_closing.wed.min),
            maxTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.wed.max
                : timeConvert(validation_data.openning_closing.wed.max),
            format:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
            formatTime:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a"
          });
          break;
        case 4:
          this.setOptions({
            minTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.thur.min
                : timeConvert(validation_data.openning_closing.thur.min),
            maxTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.thur.max
                : timeConvert(validation_data.openning_closing.thur.max),
            format:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
            formatTime:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a"
          });
          break;
        case 5:
          this.setOptions({
            minTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.fri.min
                : timeConvert(validation_data.openning_closing.sat.min),
            maxTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.fri.max
                : timeConvert(validation_data.openning_closing.sat.max),
            format:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
            formatTime:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a"
          });
          break;
        case 6:
          this.setOptions({
            minTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.sat.min
                : timeConvert(validation_data.openning_closing.sat.min),
            maxTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.sat.max
                : timeConvert(validation_data.openning_closing.sat.max),
            format:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
            formatTime:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a"
          });
          break;
        default:
          this.setOptions({
            minTime: "00:00 am",
            format:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
            formatTime:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a"
          });
      }
    };

    $("#pickup-time").datetimepicker({
      datepicker: false,
      format: conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
      formatTime: conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
      step: conditional_data.time_interval
        ? parseInt(conditional_data.time_interval)
        : 5,
      scrollInput: false,
      onShow: OpeningClosingTimeLogic,
      allowTimes: conditional_data.allowed_times
    });

    /**
     * Configuratin of time picker for dropoffdate
     *
     * @since 1.0.0
     * @return null
     */
    $("#dropoff-date").change(function(e) {
      $("#dropoff-time").val("");
    });
    // start new code
    var dropDateTimeOptions = {};
    var dropDatepickerOption = {
      timepicker: false,
      scrollMonth: false,
      dayOfWeekStart: general_data.day_of_week_start
        ? general_data.day_of_week_start
        : 0,
      format: conditional_data.date_format,
      minDate: 0,
      disabledDates: CALENDAR_DATA.block_dates,
      formatDate: conditional_data.date_format,
      formatTime: "H:i",
      onShow: function(ct) {
        this.setOptions({
          minDate: $("#pickup-date").val() ? $("#pickup-date").val() : 0,
          disabledDates: final
        });
      },
      disabledWeekDays: offDays,
      i18n: {
        domain: {
          months: months,
          dayOfWeek: weekdays
        }
      },
      scrollInput: false
    };

    if (window.innerWidth <= 480) {
      $("#dropoff-date").on("click", function() {
        $("#dropoff-modal-body ").show();
        $("#drop-mobile-datepicker").datetimepicker({
          scrollMonth: false,
          inline: true,
          timepicker: false,
          minDate: 0,
          disabledWeekDays: offDays,
          i18n: {
            domain: {
              months: months,
              dayOfWeek: weekdays
            }
          },
          dayOfWeekStart: general_data.day_of_week_start
            ? general_data.day_of_week_start
            : 0,
          format: conditional_data.date_format,
          formatDate: conditional_data.date_format,
          disabledDates: CALENDAR_DATA.block_dates,
          formatTime: "H:i",
          onShow: function(ct) {
            this.setOptions({
              minDate: $("#pickup-date").val() ? $("#pickup-date").val() : 0,
              disabledDates: final
            });
          },
          onSelectDate: function(ct) {
            dropDateTimeOptions["date"] = ct;
          },
          onSelectTime: function(ct) {
            dropDateTimeOptions["time"] = ct;
          }
        });
        $("#drop-cal-close-btn").on("click", function() {
          $("#dropoff-modal-body ").hide();
          $("#dropoff-date").datetimepicker("destroy");
        });
        $("#drop-cal-submit-btn").on("click", function() {
          $("#dropoff-modal-body ").hide();

          $("#dropoff-date").datetimepicker(
            Object.assign(dropDatepickerOption, {
              value: dropDateTimeOptions["date"]
            })
          );
          $("#dropoff-time").datetimepicker({
            format:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
            formatTime:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
            value: dropDateTimeOptions["time"]
          });
          $("form.cart").trigger("change");
          $("#dropoff-date").datetimepicker("destroy");
        });
      });
    } else {
      $("#dropoff-date").datetimepicker(dropDatepickerOption);
    }
    // end new code

    /**
     * Configuratin of time picker for pickuptime
     *
     * @since 1.0.0
     * @return null
     */
    var DropOffOpeningClosingTimeLogic = function(currentDateTime) {
      var euroFormat = conditional_data.euro_format,
        selectedDay;

      if (euroFormat === "yes") {
        var date = $("#dropoff-date").val();
        var splitDate = date.split("/");
        var finalDate = `${splitDate[1]}/${splitDate[0]}/${splitDate[2]}`;
        selectedDay = new Date(finalDate).getDay();
      } else {
        selectedDay = new Date($("#dropoff-date").val()).getDay();
      }

      switch (selectedDay) {
        case 0:
          this.setOptions({
            minTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.sun.min
                : timeConvert(validation_data.openning_closing.sun.min),
            maxTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.sun.max
                : timeConvert(validation_data.openning_closing.sun.max),
            format:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
            formatTime:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a"
          });
          break;
        case 1:
          this.setOptions({
            minTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.mon.min
                : timeConvert(validation_data.openning_closing.mon.min),
            maxTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.mon.max
                : timeConvert(validation_data.openning_closing.mon.max),
            format:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
            formatTime:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a"
          });
          break;
        case 2:
          this.setOptions({
            minTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.thu.min
                : timeConvert(validation_data.openning_closing.thu.min),
            maxTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.thu.max
                : timeConvert(validation_data.openning_closing.thu.max),
            format:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
            formatTime:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a"
          });
          break;
        case 3:
          this.setOptions({
            minTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.wed.min
                : timeConvert(validation_data.openning_closing.wed.min),
            maxTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.wed.max
                : timeConvert(validation_data.openning_closing.wed.max),
            format:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
            formatTime:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a"
          });
          break;
        case 4:
          this.setOptions({
            minTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.thur.min
                : timeConvert(validation_data.openning_closing.thur.min),
            maxTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.thur.max
                : timeConvert(validation_data.openning_closing.thur.max),
            format:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
            formatTime:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a"
          });
          break;
        case 5:
          this.setOptions({
            minTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.fri.min
                : timeConvert(validation_data.openning_closing.fri.min),
            maxTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.fri.max
                : timeConvert(validation_data.openning_closing.fri.max),
            format:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
            formatTime:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a"
          });
          break;
        case 6:
          this.setOptions({
            minTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.sat.min
                : timeConvert(validation_data.openning_closing.sat.min),
            maxTime:
              conditional_data.time_format === "24-hours"
                ? validation_data.openning_closing.sat.max
                : timeConvert(validation_data.openning_closing.sat.max),
            format:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
            formatTime:
              conditional_data.time_format === "24-hours" ? "H:i" : "h:i a"
          });
          break;
        default:
          this.setOptions({
            minTime: "00:00"
          });
      }
    };

    $("#dropoff-time").datetimepicker({
      datepicker: false,
      format: conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
      formatTime: conditional_data.time_format === "24-hours" ? "H:i" : "h:i a",
      step: conditional_data.time_interval
        ? parseInt(conditional_data.time_interval)
        : 5,
      scrollInput: false,
      onShow: DropOffOpeningClosingTimeLogic,
      allowTimes: conditional_data.allowed_times
    });

    function timeConvert(time) {
      if (time === "24:00") {
        return "11:59 pm";
      }
      time = time
        .toString()
        .match(/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [time];
      if (time.length > 1) {
        time = time.slice(1);
        time[5] = +time[0] < 12 ? " am" : " pm";
        time[0] = +time[0] % 12 || 12;
      }
      return time.join("");
    }
  }

  /**
   * Initialize calendar
   *
   * @since 1.0.0
   * @version 5.9.0
   * @return null
   */
  calendarInit();

  /**
   * Configuratin others pluins
   *
   * @since 1.0.0
   * @return null
   */
  $(".redq-select-boxes").chosen();
  $(".price-showing").hide();
  $(".rnb-pricing-plan-link").click(function(e) {
    e.preventDefault();
    $(".price-showing").slideToggle();
  });

  // RnB modal
  $("#showBooking").on("click", function() {
    $("#animatedModal").toggleClass("zoomIn");
    $("body").addClass("rnbOverflow");
  });

  $(".close-animatedModal i").on("click", function() {
    $("#animatedModal").removeClass("zoomIn");
    $("body").removeClass("rnbOverflow");
  });
  var wizardLength = $("#rnbSmartwizard").find("h3").length;

  var wizard = $("#rnbSmartwizard").steps({
    stepsOrientation: "vertical",
    headerTag: "h3",
    bodyTag: "section",
    transitionEffect: "fade",
    enableFinishButton: true,
    autoFocus: true,
    onFinished: function(event, currentIndex) {
      event.preventDefault();
      $("li.book-now").show();
    },
    onStepChanged: function(event, currentIndex, priorIndex) {
      switch (currentIndex) {
        case 0:
          $(".title-wrapper h3").html(
            layout_two_labels.location.location_top_heading
          );
          $(".title-wrapper p").html(
            layout_two_labels.location.location_top_desc
          );
          $("#rnbSmartwizard .actions ul li:nth-child(2)").removeClass(
            "disabledNextOnModal"
          );
          $("li.book-now").hide();
          return true;
        case 1:
          $(".title-wrapper h3").html(
            layout_two_labels.datetime.date_top_heading
          );
          $(".title-wrapper p").html(layout_two_labels.datetime.date_top_desc);
          // ****************** modal form validation start ******************
          $("#rnbSmartwizard .actions ul li:nth-child(2)")
            .addClass("disabled disabledNextOnModal")
            .attr("aria-disabled", "true");
          $(".date-error-message").hide();

          var rnb_error_data = window.Rnb_error_check
            ? window.Rnb_error_check
            : false;
          if (rnb_error_data !== false) {
            $("#rnbSmartwizard .actions ul li:nth-child(2)")
              .removeClass("disabled disabledNextOnModal")
              .addClass("proceedOnModal")
              .attr("aria-disabled", "false");
          } else {
            $("#rnbSmartwizard .actions ul li:nth-child(2)")
              .addClass("disabled disabledNextOnModal")
              .attr("aria-disabled", "true");
          }
          // ****************** modal form validation end ******************
          $("li.book-now").hide();
        case 2:
          $(".title-wrapper h3").html(
            layout_two_labels.resource.resource_top_heading
          );
          $(".title-wrapper p").html(
            layout_two_labels.resource.resource_top_desc
          );
          $("li.book-now").hide();
          return true;
        case 3:
          $(".title-wrapper h3").html(
            layout_two_labels.person.person_top_heading
          );
          $(".title-wrapper p").html(layout_two_labels.person.person_top_desc);
          $("li.book-now").hide();
          return true;
        case 4:
          $(".title-wrapper h3").html(
            layout_two_labels.deposit.deposit_top_heading
          );
          $(".title-wrapper p").html(
            layout_two_labels.deposit.deposit_top_desc
          );
          $("li.book-now").hide();
          return true;
        case 5:
          $(".title-wrapper h3").html("Summary");
          $(".title-wrapper p").html("Summary Desc");
          return true;
        default:
          return true;
      }
      return false;
    },
    onInit: function(event, currentIndex) {
      calendarInit();
      return true;
    },
    labels: {
      cancel: "Cancel",
      current: "current step:",
      pagination: "Pagination",
      finish: "Finish Process",
      next: "Next",
      previous: "Previous",
      loading: "Loading ..."
    }
  });

  var $input = $(
    '<li class="book-now" style="display: none;"><button type="submit" class="single_add_to_cart_button redq_add_to_cart_button btn-book-now button alt">Book Now</button></li>'
  );
  $input.appendTo($("ul[aria-label=Pagination]"));

  // Checkbox Class Toggle
  $('.rnb-control-checkbox input[type="checkbox"]').change(function() {
    $("label[for=rnb-resource-" + $(this).data("items") + "]").toggleClass(
      "checked"
    );
  });

  $('.rnb-control-checkbox.rnb-deposit-label input[type="checkbox"]').change(
    function() {
      $("label[for=rnb-deposit-" + $(this).data("items") + "]").toggleClass(
        "checked"
      );
    }
  );

  // Radio Class Toggle
  $('.rnb-control-radio.rnb-adult-label input[type="radio"]').bind(
    "click",
    function() {
      $("label.rnb-control-radio.rnb-adult-label").removeClass("checked");
      $("label[for=rnb-adult-" + $(this).data("items") + "]").addClass(
        "checked"
      );
    }
  );

  $('.rnb-control-radio.rnb-child-label input[type="radio"]').bind(
    "click",
    function() {
      $("label.rnb-control-radio.rnb-child-label").removeClass("checked");
      $("label[for=rnb-child-" + $(this).data("items") + "]").addClass(
        "checked"
      );
    }
  );

  /**
   * Configuratin for RFQ
   *
   * @since 1.0.0
   * @return null
   */
  $(".quote-submit i").hide();
  $("#quote-content-confirm").magnificPopup({
    items: {
      src: "#quote-popup",
      type: "inline"
    },
    preloader: false,
    focus: "#quote-username",

    // When elemened is focused, some mobile browsers in some cases zoom in
    // It looks not nice, so we disable it:
    callbacks: {
      beforeOpen: function() {
        if ($(window).width() < 700) {
          this.st.focus = false;
        } else {
          this.st.focus = "#quote-username";
        }
      },
      open: function() {}
    }
  });

  $(".quote-submit").on("click", function(e) {
    e.preventDefault();
    $(".quote-submit i").show();
    var cartData = $(".cart").serializeArray();
    var modalForm = {
      quote_username: $("#quote-username").val(),
      quote_password: $("#quote-password").val(),
      quote_first_name: $("#quote-first-name").val(),
      quote_last_name: $("#quote-last-name").val(),
      quote_email: $("#quote-email").val(),
      quote_phone: $("#quote-phone").val(),
      quote_message: $("#quote-message").val()
    };

    var product_id = $(".product_id").val(),
      quote_price = $(".quote_price").val();

    var errorMsg = "";
    var proceed = true;
    $(
      "#quote-popup input[required=true], #quote-popup textarea[required=true]"
    ).each(function() {
      if (!$.trim($(this).val())) {
        //if this field is empty
        var atrName = $(this).attr("name");

        if (atrName == "quote-first-name") {
          errorMsg += `${translatedStrings.quote_first_name}<br>`;
        } else if (atrName == "quote-email") {
          errorMsg += `${translatedStrings.quote_email}<br>`;
        } else if (atrName == "quote-message") {
          errorMsg += `${translatedStrings.quote_message}<br>`;
        } else if (atrName == "quote-last-name") {
          errorMsg += `${translatedStrings.quote_last_name}<br>`;
        } else if (atrName == "quote-phone") {
          errorMsg += `${translatedStrings.quote_phone}<br>`;
        } else if (atrName == "quote-username") {
          errorMsg += `${translatedStrings.quote_user_name}<br>`;
        } else if (atrName == "quote-password") {
          errorMsg += `${translatedStrings.quote_password}<br>`;
        }
        proceed = false; //set do not proceed flag
      }
      //check invalid email
      var email_reg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
      if (
        $(this).attr("type") == "email" &&
        !email_reg.test($.trim($(this).val()))
      ) {
        $(this)
          .parent()
          .addClass("has-error");
        proceed = false; //set do not proceed flag
        errorMsg += "Email Must be valid & required!<br>";
      }

      if (errorMsg !== undefined && errorMsg !== "") {
        $(".quote-modal-message")
          .slideDown()
          .html(errorMsg);
        $(".quote-submit i").hide();
      }
    });
    if (proceed) {
      cartData.push({ forms: modalForm });
      var quote_params = {
        action: "redq_request_for_a_quote",
        form_data: cartData,
        product_id: product_id,
        quote_price: quote_price
      };

      $.ajax({
        url: REDQ_RENTAL_API.ajax_url,
        dataType: "json",
        type: "POST",
        data: quote_params,
        success: function(response) {
          $(".quote-modal-message").html(response.message);
          if (response.status_code === 200) {
            //$("#quote-popup").magnificPopup("close");
            $(".quote-submit i").hide();
          }
        }
      });
    }
  });

  /**
   * Configuratin for select fields validation checking
   *
   * @since 3.0.4
   * @return null
   */
  $(".pickup_location").on("change", function() {
    var val = $(this).val();
    if (val) {
      $(".pickup_location")
        .next(".select2-container")
        .css("border", "1px solid #bbb");
    }
  });

  $(".dropoff_location").on("change", function() {
    var val = $(this).val();
    if (val) {
      $(".dropoff_location")
        .next(".select2-container")
        .css("border", "1px solid #bbb");
    }
  });

  $(".additional_adults_info").on("change", function() {
    var val = $(this).val();
    if (val) {
      $(".additional_adults_info")
        .next(".select2-container")
        .css("border", "1px solid #bbb");
    }
  });

  $("#pickup-time").on("change", function() {
    var val = $(this).val();
    if (val) {
      $("#pickup-time").css("border", "1px solid #bbb");
    }
  });

  $("#dropoff-time").on("change", function() {
    var val = $(this).val();
    if (val) {
      $("#dropoff-time").css("border", "1px solid #bbb");
    }
  });

  $(".redq_add_to_cart_button").on("click", function(e) {
    var flag = false,
      validate_messages = [];

    if (validation_data.pickup_location === "open") {
      var plocation = $(".pickup_location").val();
      if (!plocation && typeof plocation != "undefined") {
        $(".pickup_location")
          .next(".chosen-container")
          .css("border", "1px solid red");
        validate_messages.push(translatedStrings.pickup_loc_required);
        flag = true;
      }
    }
    if (validation_data.return_location === "open") {
      var dlocation = $(".dropoff_location").val();
      if (!dlocation && typeof dlocation != "undefined") {
        $(".dropoff_location")
          .next(".chosen-container")
          .css("border", "1px solid red");
        validate_messages.push(translatedStrings.dropoff_loc_required);
        flag = true;
      }
    }
    if (validation_data.person === "open") {
      var person = $(".additional_adults_info").val();
      if (!person && typeof person != "undefined") {
        $(".additional_adults_info")
          .next(".chosen-container")
          .css("border", "1px solid red");
        validate_messages.push(translatedStrings.adult_required);
        flag = true;
      }
    }
    if (validation_data.pickup_time === "open") {
      var pickup_time = $("#pickup-time").val();
      if (!pickup_time && typeof pickup_time != "undefined") {
        $("#pickup-time").css("border", "1px solid red");
        validate_messages.push(translatedStrings.pickup_time_required);
        flag = true;
      }
    }
    if (validation_data.return_time === "open") {
      var return_time = $("#dropoff-time").val();
      if (!return_time && typeof return_time != "undefined") {
        $("#dropoff-time").css("border", "1px solid red");
        validate_messages.push(translatedStrings.dorpoff_time_required);
        flag = true;
      }
    }

    if (flag && validate_messages.length) {
      var preWrapper = '<ul class="validate-notice woocommerce-error">',
        postWrapper = "</ul>",
        notices = validate_messages.map(notice => {
          return `<li>${notice}</li>`;
        }),
        validateMarkup = `${preWrapper} ${notices.join(" ")} ${postWrapper}`;

      $(".rnb-notice").html(validateMarkup);
    }

    if (flag === true) e.preventDefault();
  });
});
