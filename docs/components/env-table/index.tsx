import React from 'react';
import styles from './style.module.css'

export const createMarkup = (htmlContent: string) => {
    return {__html: htmlContent};
};

type ColumnConfig = {
    key: string;
    header: string;
    headerClassName?: string;
    cellClassName?: string;
    render?: (value: any, row: any) => React.ReactNode;
};

export function OptionTable({
    options,
    columns,
}: {
    options: Record<string, any>[];
    columns: ColumnConfig[];
}) {
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
                    {columns.map(({header, headerClassName}) => (
                        <th key={header} className={`py-2 font-semibold ${headerClassName || ''}`}>
                            {header}
                        </th>
                    ))}
                </tr>
                </thead>
                <tbody className="align-baseline text-gray-900 dark:text-gray-100">
                {options.map((row, rowIndex) => (
                    <tr
                        key={rowIndex}
                        className="border-b border-gray-100 dark:border-neutral-700/50"
                    >
                        {columns.map(({key, cellClassName, render}) => (
                            <td key={key}
                                className={cellClassName}>
                                {render ? render(row[key], row) : row[key]}
                            </td>
                        ))}
                    </tr>
                ))}
                </tbody>
            </table>
        </div>
    );
}
