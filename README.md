Linux Resource Usage Monitor with Web-based GUI and charts

---

![Demo](demo.png "Demo")

## Getting started
Run local php server: 
```bash
php -S 127.0.0.1:8080
```

Dashboard: [`http://127.0.0.1:8080/index.html`](http://127.0.0.1:8080/index.html)

View RAM usage (RSS): [`http://127.0.0.1:8080/ram.php`](http://127.0.0.1:8080/ram.php)

View SWAP usage: [`http://127.0.0.1:8080/swap.php`](http://127.0.0.1:8080/swap.php)

View CPU frequency: [`http://127.0.0.1:8080/cpu.php`](http://127.0.0.1:8080/cpu.php)

### RAM usage

It groups processes by command (without parameters).

Also, it assumes that any `java` process is JetBrains IDEA (that was my case).

### CPU usage

It detects maximum and minimum frequency.

Also, it displays annotation lines for base frequency, turbo boost for 1 active core and 2.

**NOTE**: these annotations are specific per CPU. I have them configured for Intel Core i5-2430M. You should change them in [`cpu.php`](./cpu.php) file, search for 
```js
new Chart(ctx, {
  //...
  options: {
    //...
    annotations: {
      annotations: [
        // Configure here
      ]      
    }
  }
})
```

##### TODO:
- [ ] Update the [Demo](./demo.png) with the Dashboard screen-shot. 
