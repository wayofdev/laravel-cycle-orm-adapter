import "asciinema-player/dist/bundle/asciinema-player.css";
import React, { useEffect, useRef } from 'react';

const AsciinemaPlayer = ({ src, options = {}, ...rest }) => {
    const ref = useRef(null);

    useEffect(() => {
        if (typeof window !== 'undefined') {
            import('asciinema-player').then((module) => {
                if (ref.current) {
                    module.create(src, ref.current, options);
                }
            });
        }
    }, [src, JSON.stringify(options)]);

    return <div ref={ref} {...rest} />;
};

export default AsciinemaPlayer;
