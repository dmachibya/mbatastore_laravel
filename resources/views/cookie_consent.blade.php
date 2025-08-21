<style type="text/css">
  #consentBox {
    position: fixed;
    bottom: 0px;
    left: 0px;
    background: #f3f3f3;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    text-align: center;
    z-index: 9999;
  }

  #consentBox.hide {
    opacity: 0;
    pointer-events: none;
    transform: scale(0.8);
    transition: all 0.3s ease;
  }

  ::selection {
    color: #fff;
    background: #229a0f;
  }

  #consentContent p {
    color: #858585;
    margin: 10px 0 20px 0;
  }

  #consentContent .buttons {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 20px;
  }

  .consentButton,
  .rejectButton {
    padding: 12px 30px;
    border: none;
    outline: none;
    color: #fff;
    font-size: 16px;
    font-weight: 500;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .consentButton {
    background: var(--primary-color);
    margin-right: 10px;
  }

  .rejectButton {
    color: #111211;
    background: transparent;
    border: 2px solid var(--primary-color);
    text-decoration: none;
  }
</style>

<div id="consentBox">
  <div id="consentContent">
    <p>
      {!! trans('app.cookie_consent_message') !!}
      <a href="{{ get_page_url(\App\Models\Page::PAGE_PRIVACY_POLICY) }}" target="_blank">{{ trans('app.cookies_terms') }}</a>
    </p>

    <div class="buttons">
      <button class="consentButton">
        {{ trans('app.cookie_consent_agree') }}
      </button>

      <button class="rejectButton">
        {{ trans('app.reject') }}
      </button>
    </div>
  </div>
</div>

<script>
  const consentBox = document.getElementById("consentBox");
  const acceptBtn = document.querySelector(".consentButton");
  const rejectBtn = document.querySelector(".rejectButton");
  const domain = "{{ config('session.domain') ?? request()->getHost() }}";
  const cookieName = "{{ config('gdpr.cookie.name') }}";

  // When accept
  acceptBtn.onclick = () => {
    setCookie(cookieName, 'accept', {{ config('gdpr.cookie.lifetime') }});
    hideCookieDialog();
  };

  // When reject
  rejectBtn.onclick = () => {
    setCookie(cookieName, 'reject', 1);
    hideCookieDialog();
  };

  function setCookie(name, value, expirationInDays) {
    const date = new Date();
    date.setTime(date.getTime() + (expirationInDays * 24 * 60 * 60 * 1000));

    document.cookie = name + '=' + value +
      ';expires=' + date.toUTCString() +
      ';domain=' + domain +
      ';path=/{{ config('session.secure') ? ';secure' : null }}';
  }

  function hideCookieDialog() {
    if (document.cookie) {
      consentBox.classList.add("hide");
    } else {
      alert("Cookie can't be set! Please" + " unblock this site from the cookie" + " setting of your browser.");
    }
  }

  let checkCookie = document.cookie.indexOf(cookieName);
  checkCookie !== -1 ? consentBox.classList.add("hide") : consentBox.classList.remove("hide");
</script>
