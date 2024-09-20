import { defineStore } from "pinia";

export const useCampainstore = defineStore("campains", {
    state: () => ({
        items: [],
        pagiantor: {},
        fetching: true,
    }),
    actions: {},
});