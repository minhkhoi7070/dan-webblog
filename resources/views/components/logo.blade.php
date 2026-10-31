{{-- resources/views/components/logo.blade.php --}}
<svg {{ $attributes->merge(['class' => 'inline-block select-none flex-shrink-0 bloub-svg']) }} width="36" height="31" viewBox="30 55 260 225" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="CloudBlog Logo">
    <style>
        .cloud-body-group {
            transform-origin: 157.5px 169.8px;
            animation: cloud-breathe 3s linear infinite;
        }
        .lobe-1 { animation: lobe-wobble-1 3s ease-in-out infinite; transform-origin: 103.57px 185.39px; }
        .lobe-2 { animation: lobe-wobble-2 3s ease-in-out infinite; transform-origin: 162.18px 197.77px; }
        .lobe-3 { animation: lobe-wobble-3 3s ease-in-out infinite; transform-origin: 218.57px 185.36px; }
        .lobe-4 { animation: lobe-wobble-4 3s ease-in-out infinite; transform-origin: 198.35px 128.98px; }
        .lobe-5 { animation: lobe-wobble-5 3s ease-in-out infinite; transform-origin: 129.37px 122.14px; }
        .eyes-track {
            animation: eyes-drift 3s linear infinite;
        }
        .eye-left-blink {
            transform-origin: 179.8px 120.6px;
            animation: eye-blink-left 3s linear infinite;
        }
        .eye-right-blink {
            transform-origin: 224.7px 106.4px;
            animation: eye-blink-right 3s linear infinite;
        }
        @keyframes cloud-breathe {
            0%, 100% { transform: translate(0px, 0px) scale(1, 1); }
            25%      { transform: translate(0px, -0.3px) scale(1.004, 1.006); }
            50%      { transform: translate(-0.2px, -0.2px) scale(1.002, 1.003); }
            83.33%   { transform: translate(-0.3px, -0.4px) scale(0.997, 0.996); }
        }
        @keyframes lobe-wobble-1 {
            0%, 100% { transform: translate(0px, 0px); }
            33%      { transform: translate(-0.6px, 0.5px); }
            75%      { transform: translate(0.5px, -0.4px); }
        }
        @keyframes lobe-wobble-2 {
            0%, 100% { transform: translate(0px, 0px); }
            30%      { transform: translate(0px, 0.8px); }
            80%      { transform: translate(-0.4px, -0.6px); }
        }
        @keyframes lobe-wobble-3 {
            0%, 100% { transform: translate(0px, 0px); }
            35%      { transform: translate(0.7px, 0.3px); }
            85%      { transform: translate(-0.5px, -0.4px); }
        }
        @keyframes lobe-wobble-4 {
            0%, 100% { transform: translate(0px, 0px); }
            25%      { transform: translate(0.4px, -0.7px); }
            75%      { transform: translate(-0.4px, 0.5px); }
        }
        @keyframes lobe-wobble-5 {
            0%, 100% { transform: translate(0px, 0px); }
            25%      { transform: translate(-0.5px, -0.8px); }
            80%      { transform: translate(0.4px, 0.6px); }
        }
        @keyframes eyes-drift {
            0%      { transform: translate(0.0px, 0.0px); }
            8.33%   { transform: translate(-0.2px, 0.3px); }
            16.67%  { transform: translate(-0.8px, 0.4px); }
            25.00%  { transform: translate(-1.4px, 0.6px); }
            33.33%  { transform: translate(-1.8px, 1.0px); }
            41.67%  { transform: translate(-2.2px, 1.3px); }
            46.67%  { transform: translate(-2.7px, 1.6px); }
            50.00%  { transform: translate(-3.2px, 1.8px); }
            53.33%  { transform: translate(-3.3px, 2.1px); }
            58.33%  { transform: translate(-3.8px, 2.3px); }
            66.67%  { transform: translate(-3.5px, 2.5px); }
            75.00%  { transform: translate(-1.7px, 2.6px); }
            83.33%  { transform: translate(0.3px, 3.0px); }
            91.67%  { transform: translate(1.3px, 3.7px); }
            98.33%  { transform: translate(1.1px, 3.9px); }
            100%    { transform: translate(0.0px, 0.0px); }
        }
        @keyframes eye-blink-left {
            0%, 46.67%   { transform: rotate(-28.3deg) scaleY(1) scaleX(1); }
            48.33%       { transform: rotate(-45deg) scaleY(0.36) scaleX(0.85); }
            50.00%       { transform: rotate(-68deg) scaleY(0.17) scaleX(0.75); }
            51.67%       { transform: rotate(-36deg) scaleY(0.68) scaleX(0.95); }
            53.33%, 100% { transform: rotate(-27.2deg) scaleY(1) scaleX(1); }
        }
        @keyframes eye-blink-right {
            0%, 46.67%   { transform: rotate(-28.3deg) scaleY(1) scaleX(1); }
            48.33%       { transform: rotate(-45deg) scaleY(0.36) scaleX(0.85); }
            50.00%       { transform: rotate(-65deg) scaleY(0.17) scaleX(0.75); }
            51.67%       { transform: rotate(-36deg) scaleY(0.68) scaleX(0.95); }
            53.33%, 100% { transform: rotate(-27.2deg) scaleY(1) scaleX(1); }
        }
        @media (prefers-reduced-motion: reduce) {
            .cloud-body-group, .lobe-1, .lobe-2, .lobe-3, .lobe-4, .lobe-5,
            .eyes-track, .eye-left-blink, .eye-right-blink {
                animation: none !important;
            }
        }
    </style>

    <!-- Thân đám mây màu #f08a24 gồm 5 khối phập phồng nhẹ nhàng -->
    <g class="cloud-body-group" fill="#f08a24">
        <circle class="lobe-1" cx="103.57" cy="185.39" r="68.64" />
        <circle class="lobe-2" cx="162.18" cy="197.77" r="76.59" />
        <circle class="lobe-3" cx="218.57" cy="185.36" r="63.47" />
        <circle class="lobe-4" cx="198.35" cy="128.98" r="55.39" />
        <circle class="lobe-5" cx="129.37" cy="122.14" r="61.44" />
    </g>

    <!-- 2 mắt màu #f9f9f9 liếc mắt tự nhiên và chớp mắt chân thực -->
    <g class="eyes-track" fill="#f9f9f9">
        <g class="eye-left-blink">
            <rect x="169.35" y="95.95" width="20.9" height="49.3" rx="10.45" ry="10.45" />
        </g>
        <g class="eye-right-blink">
            <rect x="218.35" y="81.50" width="12.7" height="49.8" rx="6.35" ry="6.35" />
        </g>
    </g>
</svg>