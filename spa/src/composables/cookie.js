export function useCookie() {
    const get = (name) => {
        const value = "; " + document.cookie;
        const parts = value.split("; " + name + "=");
        if (parts.length === 2) return parts.pop().split(";").shift();
    };

    const set = (name, value, expires) => {
        document.cookie =
            name + "=" + value + ";expires=" + expires + ";path=/";
    };

    return { get, set };
}
