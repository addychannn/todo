const animation = {
  shake: "shake 0.5s cubic-bezier(.36,.07,.19,.97) both",
  ripple: "ripple 0.5s linear infinite",
  collapse: "collapse 0.5s ease-in",
  progInc: "progInc 2s infinite",
  progDec: "progDec 2s 0.5s infinite",
  progIncV: "progIncV 2s infinite",
  progDecV: "progDecV 2s 0.5s infinite",
  progStripe: "progStripe 1s linear infinite",
  progStripeV: "progStripeV 1s linear infinite",
  pop: "pop 0.4s ease-in-out",
  sigh: "sigh 0.4s ease-in-out",
  "sigh-left": "sigh-left 0.4s ease-in-out",
  "sigh-right": "sigh-right 0.4s ease-in-out",
  "sigh-up": "sigh-up 0.4s ease-in-out",
  "sigh-down": "sigh-down 0.4s ease-in-out",
  "slide-in-right": "slide-in-right 0.4s ease-in-out",
  "slide-in-left": "slide-in-left 0.4s ease-in-out",
  "slide-in-up": "slide-in-up 0.4s ease-in-out",
  "slide-in-down": "slide-in-down 0.4s ease-in-out",
  "pop-slide-in-right": "pop-slide-in-right 0.4s ease-in-out",
  "pop-slide-in-left": "pop-slide-in-left 0.4s ease-in-out",
  "pop-slide-in-up": "pop-slide-in-up 0.4s ease-in-out",
  "pop-slide-in-down": "pop-slide-in-down 0.4s ease-in-out",
  wiggle: "wiggle 1s ease-in-out infinite",
};
const keyframes = {
  shake: {
    "10%, 90%": {
      transform: "translate3d(-1px, 0, 0)",
    },

    "20%, 80%": {
      transform: "translate3d(2px, 0, 0)",
    },

    "30%, 50%, 70%": {
      transform: "translate3d(-4px, 0, 0)",
    },

    "40%, 60%": {
      transform: "translate3d(4px, 0, 0)",
    },
  },
  ripple: {
    "0%": { width: "0px", height: "0px", opacity: 0.5 },
    "100%": { width: "500px", height: "500px", opacity: 0 },
  },
  collapse: {
    from: { "max-height": "100%" },
    to: { "max-height": "0%" },
  },
  progInc: {
    from: { left: "-5%", width: "5%" },
    to: { left: "130%", width: "100%" },
  },
  progDec: {
    from: { left: "-80%", width: "80%" },
    to: { left: "110%", width: "10%" },
  },
  progIncV: {
    from: { bottom: "-5%", height: "5%" },
    to: { bottom: "130%", height: "100%" },
  },
  progDecV: {
    from: { bottom: "-80%", height: "80%" },
    to: { bottom: "110%", height: "10%" },
  },
  progStripe: {
    "0%": {
      "background-position": "0 0",
    },
    "100%": {
      "background-position": "1rem 0",
    },
  },
  progStripeV: {
    "0%": {
      "background-position": "0 0",
    },
    "100%": {
      "background-position": "0 1rem",
    },
  },
  pop: {
    "0%": {
      transform: "scale(0)",
    },
    "50%": {
      transform: "scale(1.1)",
    },
    "100%": {
      transform: "scale(1)",
    },
  },
  sigh: {
    "0%": {
      transform: "scale(1)",
    },
    "50%": {
      transform: "scale(1.05)",
    },
    "100%": {
      transform: "scale(1)",
    },
  },
  "sigh-left": {
    "0%": {
      transform: "translateX(0) scale(1)",
    },
    "50%": {
      transform: "translateX(-10px) scale(1.05)",
    },
    "100%": {
      transform: "translateX(0) scale(1)",
    },
  },
  "sigh-right": {
    "0%": {
      transform: "translateX(0) scale(1)",
    },
    "50%": {
      transform: "translateX(10px) scale(1.05)",
    },
    "100%": {
      transform: "translateX(0) scale(1)",
    },
  },
  "sigh-up": {
    "0%": {
      transform: "translateY(0) scale(1)",
    },
    "50%": {
      transform: "translateY(-10px) scale(1.05)",
    },
    "100%": {
      transform: "translateY(0) scale(1)",
    },
  },
  "sigh-down": {
    "0%": {
      transform: "translateY(0) scale(1)",
    },
    "50%": {
      transform: "translateY(10px) scale(1.05)",
    },
    "100%": {
      transform: "translateY(0) scale(1)",
    },
  },
  "slide-in-right": {
    "0%": {
      transform: "translateX(-100%)",
    },
    "100%": {
      transform: "translateX(0)",
    },
  },
  "slide-in-left": {
    "0%": {
      transform: "translateX(100%)",
    },
    "100%": {
      transform: "translateX(0)",
    },
  },
  "slide-in-up": {
    "0%": {
      transform: "translateY(100%)",
    },
    "100%": {
      transform: "translateY(0)",
    },
  },
  "slide-in-down": {
    "0%": {
      transform: "translateY(-100%)",
    },
    "100%": {
      transform: "translateY(0)",
    },
  },
  "pop-slide-in-right": {
    "0%": {
      transform: "translateX(-100%)",
    },
    "50%": {
      transform: "translateX(10%)",
    },
    "100%": {
      transform: "translateX(0)",
    },
  },
  "pop-slide-in-left": {
    "0%": {
      transform: "translateX(100%)",
    },
    "50%": {
      transform: "translateX(-10%)",
    },
    "100%": {
      transform: "translateX(0)",
    },
  },
  "pop-slide-in-up": {
    "0%": {
      transform: "translateY(100%)",
    },
    "50%": {
      transform: "translateY(-10%)",
    },
    "100%": {
      transform: "translateY(0)",
    },
  },
  "pop-slide-in-down": {
    "0%": {
      transform: "translateY(-100%)",
    },
    "50%": {
      transform: "translateY(10%)",
    },
    "100%": {
      transform: "translateY(0)",
    },
  },
  wiggle: {
    "0%, 50%, 100%": {
      transform: "rotate(0deg)",
    },
    "25%": {
      transform: "rotate(2deg)",
    },
    "75%": {
      transform: "rotate(-2deg)",
    },
  },
};

module.exports = { animation, keyframes };
