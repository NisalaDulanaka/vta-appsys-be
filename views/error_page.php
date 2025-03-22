<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Page</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="/public/welcome.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white">
    <div class="px-8 py-8 text-white">
        <div class="rounded-md bg-gray-800 px-10 py-8">
            <div class="w-fit bg-[#161616c4] px-[12px] py-[8px] rounded-md mb-3">
                <h1 class="text-[14px]">ErrorException</h1>
            </div>
            <h1><?php echo $errorObject['message'] ?></h1>
        </div>

        <div class="rounded-md bg-gray-800 mt-8">
            <div class="flex flex-wrap overflow-hidden">
                <div class="flex-1 py-8">
                    <h1 class="px-10 mb-8 flex items-center gap-x-2">
                        <span class="material-symbols-outlined">
                            breaking_news
                        </span>
                        Exception Log
                    </h1>
                    <div class="mt-4">
                        <div class="bg-red-600 py-5 px-10">
                            <?php echo $errorObject['line'] ?>
                        </div>
                        <div class="py-5 px-10">
                            <h2 class="mb-4">Stack Trace : </h2>
                            <p class="text-gray-100">
                                <?php echo $errorObject['stack'] ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex-1 py-8 bg-[#202031]">
                    <h1 class="px-10 mb-4 text-[12px] text-gray-400">
                        <?php echo $errorObject['file'] ?>
                    </h1>
                    <div class="flex items-center justify-center" id="error-file-div">
                        <div class="flex w-[100%] h-[100%] flex-col justify-center items-center
                        bg-gradient-to-br from-violet-600 to-transparent">
                            <h1 class="text-2xl font-semibold">File Preview</h1>
                            <h2 class="text-indigo-100">Coming Soon</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>