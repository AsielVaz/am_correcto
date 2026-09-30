<?php
require_once dirname(__DIR__) . '/utils/routes.php';
?>

<!DOCTYPE html>
<html lang="es-MX">

<head>
    <!-- Title Meta -->
    <meta charset="utf-8" />
    <title>Código & Chips | <?php echo $pageTitle; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Código & chips | Administrar empresas" />
    <meta name="author" content="Código & chips" />
    <meta name="keywords"
        content="administración, código, chips, gestión, usuarios, empresas" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="robots" content="index, follow" />
    <meta name="theme-color" content="#ffffff">
    <meta name="app-base" content="<?php echo htmlspecialchars(appBasePath(), ENT_QUOTES, 'UTF-8'); ?>">

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo assetsUrl('images/logos/Logo-Codigo-Chips-Invertido.png'); ?>">

    <!-- Google Font Family link -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">

    <!-- Vendor css -->
    <link href="<?php echo assetsUrl('css/vendor.min.css'); ?>" rel="stylesheet" type="text/css" />

    <!-- Icons css -->
    <link href="<?php echo assetsUrl('css/icons.min.css'); ?>" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link href="<?php echo assetsUrl('css/style.min.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo assetsUrl('css/glass-ui.css'); ?>" rel="stylesheet" type="text/css" />

    <!-- Theme Config js -->
    <script src="<?php echo assetsUrl('js/config.js'); ?>"></script>
    <script src="<?php echo assetsUrl('js/path-resolver.js'); ?>"></script>
    <script src="<?php echo assetsUrl('js/glass-ui.js'); ?>"></script>
</head>

<body>
    <!-- START Wrapper -->
    <div class="app-wrapper">
        <style>
            body,
            html {
                height: 100%;
                margin: 0;
                padding: 0;
                overflow: hidden;
            }

            .bg-animated {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                z-index: -1;
                pointer-events: none;
            }
        </style>
        <canvas class="bg-animated" id="bgAnimated"></canvas>
        <script>
            const canvas = document.getElementById('bgAnimated');
            const ctx = canvas.getContext('2d');
            let w = window.innerWidth,
                h = window.innerHeight;
            canvas.width = w;
            canvas.height = h;

            function resize() {
                w = window.innerWidth;
                h = window.innerHeight;
                canvas.width = w;
                canvas.height = h;
                createNodes();
            }
            window.addEventListener('resize', resize);

            // Node config
            const nodeCount = 40;
            let nodes = [];

            function createNodes() {
                nodes = [];
                for (let i = 0; i < nodeCount; i++) {
                    nodes.push({
                        x: Math.random() * w,
                        y: Math.random() * h,
                        dx: (Math.random() - 0.5) * 0.7,
                        dy: (Math.random() - 0.5) * 0.7,
                        r: 3 + Math.random() * 3,
                        glow: 0.3 + Math.random() * 0.7,
                        origX: null,
                        origY: null,
                        origDx: null,
                        origDy: null,
                        freeze: false
                    });
                }
            }
            createNodes();

            // Mouse node
            let mouse = {
                x: w / 2,
                y: h / 2,
                r: 7,
                glow: 1.2,
                dx: 0,
                dy: 0,
                isMouse: true,
                active: false
            };
            canvas.addEventListener('mousemove', function(e) {
                const rect = canvas.getBoundingClientRect();
                mouse.x = e.clientX - rect.left;
                mouse.y = e.clientY - rect.top;
                mouse.active = true;
            });
            canvas.addEventListener('mouseleave', function() {
                mouse.active = false;
            });

            function drawNodes() {
                ctx.clearRect(0, 0, w, h);
                // Draw lines between close nodes (including mouse if active)
                let allNodes = nodes.slice();
                if (mouse.active) allNodes.push(mouse);
                for (let i = 0; i < allNodes.length; i++) {
                    for (let j = i + 1; j < allNodes.length; j++) {
                        const a = allNodes[i],
                            b = allNodes[j];
                        const dist = Math.hypot(a.x - b.x, a.y - b.y);
                        if (dist < 120) {
                            ctx.save();
                            ctx.strokeStyle = `rgba(120,120,120,${0.13 + 0.07 * (1 - dist/120)})`;
                            ctx.lineWidth = 1.2;
                            ctx.beginPath();
                            ctx.moveTo(a.x, a.y);
                            ctx.lineTo(b.x, b.y);
                            ctx.stroke();
                            ctx.restore();
                        }
                    }
                }
                // Draw nodes
                for (const n of allNodes) {
                    ctx.save();
                    ctx.shadowColor = 'rgba(120,120,120,0.7)';
                    ctx.shadowBlur = 12 * n.glow;
                    ctx.beginPath();
                    ctx.arc(n.x, n.y, n.r, 0, Math.PI * 2);
                    ctx.fillStyle = n.isMouse ? 'rgba(180,180,180,0.95)' : 'rgba(120,120,120,0.85)';
                    ctx.fill();
                    ctx.restore();
                }
            }

            let nodesGathering = false;
            let nodesScattering = false;
            let gatherStep = 0;
            let scatterStep = 0;
            let scatterTargets = [];

            function updateNodes() {
                if (nodesGathering) {
                    const centerX = w / 2,
                        centerY = h / 2;
                    let done = true;
                    for (const n of nodes) {
                        n.x += (centerX - n.x) * 0.18;
                        n.y += (centerY - n.y) * 0.18;
                        if (Math.abs(n.x - centerX) > 2 || Math.abs(n.y - centerY) > 2) done = false;
                    }
                    gatherStep++;
                    if (done || gatherStep > 40) {
                        nodesGathering = false;
                        nodesScattering = true;
                        scatterStep = 0;
                        scatterTargets = nodes.map(() => ({
                            x: Math.random() * w,
                            y: Math.random() * h
                        }));
                    }
                    return;
                }
                if (nodesScattering) {
                    let done = true;
                    for (let i = 0; i < nodes.length; i++) {
                        const n = nodes[i];
                        const t = scatterTargets[i];
                        n.x += (t.x - n.x) * 0.18;
                        n.y += (t.y - n.y) * 0.18;
                        if (Math.abs(n.x - t.x) > 2 || Math.abs(n.y - t.y) > 2) done = false;
                    }
                    scatterStep++;
                    if (done || scatterStep > 40) {
                        nodesScattering = false;
                    }
                    return;
                }
                for (const n of nodes) {
                    n.x += n.dx;
                    n.y += n.dy;
                    if (n.x < 0 || n.x > w) n.dx *= -1;
                    if (n.y < 0 || n.y > h) n.dy *= -1;
                    if (mouse.active) {
                        const dist = Math.hypot(n.x - mouse.x, n.y - mouse.y);
                        if (dist < 100) {
                            let angle = Math.atan2(mouse.y - n.y, mouse.x - n.x);
                            n.x += Math.cos(angle) * 0.4;
                            n.y += Math.sin(angle) * 0.4;
                        }
                    }
                }
            }
            window.bgNodesGatherAndScatter = function() {
                nodesGathering = true;
                gatherStep = 0;
            }

            function animate() {
                updateNodes();
                drawNodes();
                requestAnimationFrame(animate);
            }
            animate();
        </script>
