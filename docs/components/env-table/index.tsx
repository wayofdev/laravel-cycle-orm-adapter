import styles from './style.module.css'

export function OptionTable({ options }: { options: [string, string, string, string][] }) {
    const createMarkup = (htmlContent) => {
        return { __html: htmlContent };
    };

    return (
        <div
            className={
                '-mx-6 mb-4 mt-6 overflow-x-auto overscroll-x-contain px-6 pb-4 ' +
                styles.container
            }
        >
            <table className="w-full border-collapse text-sm">
                <thead>
                <tr className="border-b py-4 text-left dark:border-neutral-700">
                    <th className="py-2 font-semibold">Environment Variable</th>
                    <th className="py-2 pl-6 font-semibold">Available Values</th>
                    <th className="py-2 pl-6 font-semibold">Default</th>
                    <th className="px-6 py-2 font-semibold">Description</th>
                </tr>
                </thead>
                <tbody className="align-baseline text-gray-900 dark:text-gray-100">
                {options.map(([variable, values, defaultValue, description]) => (
                    <tr
                        key={variable}
                        className="border-b border-gray-100 dark:border-neutral-700/50"
                    >
                        <td className="whitespace-pre py-2 font-mono text-xs font-semibold leading-6 text-violet-600 dark:text-violet-500">
                            {variable}
                        </td>
                        <td className="whitespace-pre py-2 pl-6 font-mono text-xs font-semibold leading-6 text-slate-500 dark:text-slate-400">
                            {values}
                        </td>
                        <td className="whitespace-pre py-2 pl-6 font-mono text-xs font-semibold leading-6 text-slate-500 dark:text-slate-400">
                            {defaultValue}
                        </td>
                        <td className="py-2 pl-6" dangerouslySetInnerHTML={createMarkup(description)}></td>
                    </tr>
                ))}
                </tbody>
            </table>
        </div>
    )
}
