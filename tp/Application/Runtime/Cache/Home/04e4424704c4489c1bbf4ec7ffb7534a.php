<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <script>
        addLetter('babby');
        function addLetter(letter)
        {
            var first = letter.substr(0,1);
            first = first.toUpperCase();
            var arr = localStorage.getItem('letter_'+first);
            var resArr = letterSort(arr,letter);
            alert(resArr);
            localStorage.setItem('letter_'+first,JSON.stringify(resArr));
        }
        //将新单词在适当的位置插入
        function letterSort(arr,item)
        {
            var newArr = [];
            if(!arr)
            {
                newArr.push(item);
                return newArr;
            }
            arr = JSON.parse(arr);
//            console.log(arr);
            var hasInsert = false;
            for(var j in arr)
            {
                if(arr[j] > item && (!hasInsert))
                {
                    if(!hasInsert)
                    {
                        newArr.push(item);
                        hasInsert = true;
                    }
                }
                newArr.push(arr[j]);
            }
            if(!hasInsert)
            {
                newArr.push(item);
            }
            return newArr;
        }
    </script>
</head>
<body>
333
</body>
</html>