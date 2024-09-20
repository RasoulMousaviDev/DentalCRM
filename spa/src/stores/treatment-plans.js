import { defineStore } from "pinia";

export const useTreatmentPlansStore = defineStore("treatment-plans", {
    state: () => ({
        items: [],
        pagiantor: {},
        fetching: true,
    }),
    actions: {},
});