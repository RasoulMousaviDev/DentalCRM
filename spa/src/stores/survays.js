import { defineStore } from "pinia";

export const useSurvaysStore = defineStore("survays", {
    state: () => ({
        items: [],
        pagiantor: {},
        fetching: true,
    }),
    actions: {},
});