/**
 * @author Jhyden Jhoe Marx M. Ellana
 * @version 1.0
 */

let jvalidate = class {};

let countableTypes = ["object", "string", "number", "bigint"];
let uncountableTypes = ["boolean", "undefined"];

let errorMessages = {
  required: "{0} is required",
  alpha: "{0} must be alphabetic",
  alphanum: "{0} must be alphanumeric",
  email: "{0} must be a valid email",
  url: "{0} must be a valid url",
  number: "{0} must be a valid number",
  minlength: "{0} must be at least {1} characters long",
  maxlength: "{0} must be at most {1} characters long",
  min: "{0} must be greater than or equal to {1}",
  max: "{0} must be less than or equal to {1}",
  file: "{0} must be a valid file",
  mime: "{0} must be a valid {1} file",
  date: "{0} must be a valid date",
  before: "{0} must be before {1}",
  after: "{0} must be after {1}",
  before_or_equal: "{0} must be before or equal to {1}",
  after_or_equal: "{0} must be after or equal to {1}",
  in: "{0} must be one of the following values: {1}",
  notIn: "{0} must not be one of the following values: {1}",
  password: "{0} is not in a valid form",
  username: "{0} is not in a valid form",
  regex: "{0} is not in a valid form",
};

let validators = {
  required: {
    errorMessage: errorMessages.required,
    method: function (value) {
      if (!!value && typeof value == "object") {
        return Array.isArray(value) ? value.length > 0 : true;
      }
      return !!value;
    },
  },
  alpha: {
    errorMessage: errorMessages.alpha,
    method: function (value) {
      if (value === null || value === undefined || value === "") {
        return true;
      }
      return /^[a-zA-Z]+$/.test(value);
    },
  },
  alphanum: {
    errorMessage: errorMessages.alphanum,
    method: function (value) {
      if (value === null || value === undefined || value === "") {
        return true;
      }
      return /^[a-zA-Z0-9]+$/.test(value);
    },
  },
  email: {
    errorMessage: errorMessages.email,
    method: function (value) {
      if (value && value.length > 0) {
        let reg =
          /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return reg.test(value);
      }
      return true;
    },
  },
  url: {
    errorMessage: errorMessages.url,
    method: function (value) {
      if (value) {
        let reg = /^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([/\w .-]*)*\/?$/;
        return reg.test(value);
      }
    },
  },
  number: {
    errorMessage: errorMessages.number,
    method: function (value) {
      if (value) {
        return !isNaN(value);
      }
    },
  },
  minLength: {
    errorMessage: errorMessages.minlength,
    method: function (value, minLength) {
      let countable = countableTypes.indexOf(typeof value) > -1;
      if (countable && !!value && !(value instanceof Date)) {
        if (typeof value === "number") {
          return `${value}`.length >= minLength;
        }
        return (value?.length ?? 0) >= minLength;
      }
      return true;
    },
  },
  maxLength: {
    errorMessage: errorMessages.maxlength,
    method: function (value, maxLength) {
      let countable = countableTypes.indexOf(typeof value) > -1;
      if (countable && !!value && !(value instanceof Date)) {
        if (typeof value === "number") {
          return (`${value}`.length || 0) <= maxLength;
        }
        return (value?.length || 0) <= maxLength;
      }
      return true;
    },
  },
  min: {
    errorMessage: errorMessages.min,
    method: function (value, min) {
      if (value < min) {
        return false;
      }
      return true;
    },
  },
  max: {
    errorMessage: errorMessages.max,
    method: function (value, max) {
      if (value > max) {
        return false;
      }
      return true;
    },
  },
  file: {
    errorMessage: errorMessages.file,
    method: function (value) {
      if (value) {
        return value instanceof File;
      }
    },
  },
  mime: {
    errorMessage: errorMessages.mime,
    method: function (value, mime) {
      if (value) {
        let reg = new RegExp(mime);
        return reg.test(value);
      }
    },
  },
  date: {
    errorMessage: errorMessages.date,
    method: function (value) {
      if (value) {
        return !isNaN(Date.parse(value));
      }
    },
  },
  before: {
    errorMessage: errorMessages.before,
    method: function (value, before) {
      if (value) {
        return new Date(value) < new Date(before);
      }
    },
  },
  after: {
    errorMessage: errorMessages.after,
    method: function (value, after) {
      if (value) {
        return new Date(value) > new Date(after);
      }
    },
  },
  before_or_equal: {
    errorMessage: errorMessages.before_or_equal,
    method: function (value, before_or_equal) {
      if (value) {
        return new Date(value) <= new Date(before_or_equal);
      }
    },
  },
  after_or_equal: {
    errorMessage: errorMessages.after_or_equal,
    method: function (value, after_or_equal) {
      if (value) {
        return new Date(value) >= new Date(after_or_equal);
      }
    },
  },
  in: {
    errorMessage: errorMessages.in,
    method: function (value, values) {
      if (value) {
        return values.includes(value);
      }
    },
  },
  notIn: {
    errorMessage: errorMessages.notIn,
    method: function (value, values) {
      if (value) {
        return !values.includes(value);
      }
    },
  },
  password: {
    errorMessage: errorMessages.password,
    method: function (value) {
      if (value) {
        return /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/.test(value);
      }
    },
  },
  username: {
    errorMessage: errorMessages.username,
    method: function (value) {
      if (value) {
        return /^(?=[a-zA-Z0-9._]{6,20}$)(?!.*[_@.]{2})[^_@.].*[^_@.]$/.test(
          value
        );
      }
    },
  },
  regex: {
    errorMessage: errorMessages.regex,
    method: function (value, regex) {
      let _regex = new RegExp(regex);
      return !!value ? _regex.test(value) : true;
    },
  },
};

jvalidate.prototype.Validators = validators;

export default jvalidate;
