import moment from "moment-jalaali";

class MyDate extends Date {
    static type = "jalali";

    constructor(...args) {
        if (args.length == 3 && MyDate.type == "jalali") {
            args[1]++;
            const date = moment(args.join("-"), "jYYYY-jM-jDD");
            args = date.format("YYYY-M-DD").split("-").map(Number);
            args[1]--;    
        }
        super(...args);
    }

    getDate = () => {
        if (MyDate.type == "jalali") {
            return +moment(super.getTime()).format("jD");
        } else return super.getDate();
    };
    getMonth = () => {
        if (MyDate.type == "jalali") {
            return +moment(super.getTime()).format("jM") - 1;
        } else return super.getMonth();
    };
    getFullYear = () => {
        if (MyDate.type == "jalali") {
            return +moment(super.getTime()).format("jYYYY");
        } else return super.getFullYear();
    };
    setDate = (date) => {
        if (MyDate.type == "jalali") {
            const year = this.getFullYear();
            const month = this.getMonth();
            date = +moment(`${year}-${month+1}-${date}`, "jYYYY-jMM-jDD").format(
                "D"
            );
            super.setDate(date);
        } else super.setDate(date);
    };
    setMonth = (month) => {
        if (MyDate.type == "jalali") {
            const year = this.getFullYear();
            const date = this.getDate();
            month = +moment(`${year}-${month + 1}-${date}`, "jYYYY-jM-jD").format(
                "M"
            );
            super.setMonth(month - 1);
        } else super.setMonth(month);
    };
    setFullYear = (year) => {
        if (MyDate.type == "jalali") {
            const month = this.getMonth();
            const date = this.getDate();
            const m = moment(`${year}-${month+1}-${date}`, "jYYYY-jM-jDD")
            year = +m.format("YYYY");
            
            if(+m.format("M") < 3) year++
            super.setFullYear(year);
        } else super.setFullYear(year);
    };
    getMonthName = () => {
        return super.toLocaleDateString(
            MyDate.type == "jalali" ? "fa-IR" : undefined,
            { month: "long" }
        );
    };

    static getWeekdays = () => {
        const date = new Date(1, 1, 1);
        const formatter = new Intl.DateTimeFormat(
            MyDate.type == "jalali" ? "fa-IR" : undefined,
            { weekday: "narrow" }
        );
        return [...Array(7).keys()].map((i) => {
            date.setDate(i + 1);
            return formatter.format(date);
        });
    };
}

export default MyDate;