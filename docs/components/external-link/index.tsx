import React from 'react';
import { ArrowTopRightOnSquareIcon } from "@heroicons/react/20/solid";

const ExternalLink = ({ href, children }) => {
    return (
        <a href={href} rel="noopener" target="_blank" className="nx-text-primary-600 nx-underline nx-decoration-from-font [text-underline-position:from-font] inline-flex items-center gap-0.5">
            {children}
            <ArrowTopRightOnSquareIcon className="size-4" />
        </a>
    );
};

export default ExternalLink;
