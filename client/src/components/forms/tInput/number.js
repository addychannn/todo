export default {
  name: "Number",
  isValid: (input, options = {}) => {
    if (options.min !== undefined && input < options.min) {
      return false;
    }
    if (options.max !== undefined && input > options.max) {
      return false;
    }
    if (`${input}`.length > options.maxLength) {
      return false;
    }
    if (options.integer && !/^-?\d+$/.test(input)) {
      return false;
    }
    if (options.float && !/^-?\d+\.?\d*$/.test(input)) {
      return false;
    }
    return /^-?\d+\.?\d*$/.test(input);
  },
  parse: (input) => {
    return input * 1;
  },
};
