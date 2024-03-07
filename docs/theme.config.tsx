import React from 'react'
import {DocsThemeConfig, useTheme} from 'nextra-theme-docs'


const Logo = () => {
    const {theme} = useTheme();
    const title = 'Laravel CycleORM Adapter';

    const lightLogo = (
        <svg className="nx-h-8 nx-w-auto" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink"
             version="1.1" id="Layer_1"
             x="0px" y="0px" xmlSpace="preserve"
             viewBox="881.02 445.23 157.96 189.54">
            <style>
                {`.st0{fill:#FFFFFF;}
        .st1{fill:#6495ED;}
        .st2{fill:#ADFF2F;}
        .st3{fill:#87CEEB;}`}
            </style>
            <g>
                <path
                    d="M881.02,603.19V445.23h31.58v157.96H881.02z M912.61,634.77v-31.58h31.58v31.58H912.61z M944.19,603.19v-63.17h31.58v63.17   H944.19z M975.77,634.77v-31.58h31.58v31.58H975.77z M1007.36,603.19V445.23h31.62v157.96H1007.36z"></path>
                <rect x="975.77" y="603.19" className="st2" width="31.58" height="31.58"></rect>
                <rect x="944.19" y="540.02" className="st3" width="31.58" height="63.17"></rect>
                <rect x="881.02" y="445.23" className="st1" width="31.58" height="157.96"></rect>
            </g>
        </svg>
    );

    // Dark theme logo
    const darkLogo = (
        <svg className="nx-h-8 nx-w-auto" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1"
             x="0px" y="0px" xmlSpace="preserve"
             viewBox="881.02 445.23 157.96 189.54">
            <style>
                {`   .st0{fill:#FFFFFF;} .st1{fill:#6495ED;} .st2{fill:#ADFF2F;} .st3{fill:#87CEEB;}`}
            </style>
            <g>
                <path className="st0"
                      d="M881.02,603.19V445.23h31.58v157.96H881.02z M912.61,634.77v-31.58h31.58v31.58H912.61z M944.19,603.19v-63.17   h31.58v63.17H944.19z M975.77,634.77v-31.58h31.58v31.58H975.77z M1007.36,603.19V445.23h31.62v157.96H1007.36z"></path>
                <rect x="975.77" y="603.19" className="st2" width="31.58" height="31.58"></rect>
                <rect x="944.19" y="540.02" className="st3" width="31.58" height="63.17"></rect>
                <rect x="881.02" y="445.23" className="st1" width="31.58" height="157.96"></rect>
            </g>
        </svg>
    );

    return (
        <div className="nx-w-full nx-flex nx-items-center">
            <div className="nx-flex nx-items-center">
                {theme === 'light' ? lightLogo : darkLogo}
                <span
                    className="nx-ml-2 font-semibold whitespace-nowrap">Laravel CycleORM Adapter</span>
            </div>
        </div>
    );
}


const config: DocsThemeConfig = {
    logo: Logo,
    project:
        {
            link: 'https://github.com/wayofdev/laravel-cycle-orm-adapter',
        }
    ,
    docsRepositoryBase: 'https://github.com/wayofdev/laravel-cycle-orm-adapter/tree/master/docs',
    footer:
        {
            text: 'Laravel CycleORM Adapter Documentation',
        }
    ,
}

export default config
