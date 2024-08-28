import Echo from "laravel-echo";
import { api } from "./axios.js";
import Pusher from "pusher-js";

window.Pusher = Pusher;

const baseURL = import.meta.env.VITE_SERVER;

const options = Object.assign(
  {
    broadcaster: "pusher",
    key: import.meta.env.VITE_WEBSOCKETS_KEY,
    wsHost: import.meta.env.VITE_WEBSOCKETS_URL,
    wsPort: import.meta.env.VITE_WEBSOCKETS_PORT,
    forceTLS: import.meta.env.VITE_WEBSOCKETS_FORCE_TLS == "true",
    disableStats: import.meta.env.VITE_WEBSOCKETS_DISABLE_STATS == "true",
    cluster: import.meta.env.VITE_WEBSOCKETS_CLUSTER,
    authEndpoint: baseURL + "/broadcasting/auth",
    encrypted: true,
    enabledTransports: ["ws", "wss"],
    authorizer: (channel, options) => {
      return {
        authorize: (socketId, callback) => {
          api
            .post("/broadcasting/auth", {
              socket_id: socketId,
              channel_name: channel.name,
            })
            .then((response) => {
              callback(false, response.data);
            })
            .catch((error) => {
              callback(true, error);
            });
        },
      };
    },
  },
  {}
);
window.Echo = new Echo(options);
