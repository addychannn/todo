export default {
  name: "String",
  isValid: (input, options = {}) => {
    return !(
      options.maxLength !== undefined && input.length > options.maxLength
    );
  },
  parse: (input) => {
    return input;
  },
};
