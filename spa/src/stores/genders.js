import { defineStore } from "pinia";

export const useGendersStore = defineStore("genders", {
    state: () => ({
        items: ['male', 'female'],
    }),

})